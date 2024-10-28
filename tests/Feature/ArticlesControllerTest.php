<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticlesControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_get_category_successfully()
    {
        $user = User::factory()->create([
            'email' => 'testuser@gmail.com',
            'password' => 'Password@123',
        ]);

        $categories = Category::factory()->count(3)->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Success',
                'data' => $categories->toArray(),
            ]);
    }

    public function test_get_category_unauthenticated()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'error' => 'Unauthenticated',
            ]);
    }

    public function test_index_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $category = Category::factory()->create();

        foreach (range(1, 5) as $i) {
            Article::create([
                'title' => 'Sample Article ' . $i,
                'content' => 'Content for article ' . $i,
                'author' => 'Author ' . $i,
                'source' => 'Source ' . $i,
                'category_id' => $category->id,
                'unique_identifier' => Str::uuid()->toString(),
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/articles');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Success',
                'data' => [
                    'current_page' => 1,
                    'data' => Article::all()->toArray(),
                    'per_page' => 10,
                    'total' => 5,
                ],
            ]);
    }

    public function test_index_with_keyword_filter()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $category = Category::factory()->create();

        Article::create([
            'title' => 'Welcome to PHP',
            'content' => 'content about PHP.',
            'author' => 'Author Name',
            'source' => 'News API',
            'category_id' => $category->id,
            'unique_identifier' => Str::uuid(),
        ]);

        Article::create([
            'title' => 'Other News',
            'content' => 'something else.',
            'author' => 'abc author',
            'source' => 'abc source',
            'category_id' => $category->id,
            'unique_identifier' => Str::uuid(),
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/articles?keyword=PHP');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['title' => 'Welcome to PHP'])
            ->assertJsonMissing(['title' => 'Other News']);
    }

    public function test_index_unauthenticated()
    {
        $response = $this->getJson('/api/articles');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'error' => 'Unauthenticated',
            ]);
    }

    public function test_index_with_invalid_filters()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/articles?keyword=Sample&category_id=999');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The selected category id is invalid.',
            ]);
    }

    //retrive single article api test
    public function test_show_article_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $category = Category::factory()->create();

        $article = Article::create([
            'title' => 'Welcome to Laravel',
            'content' => 'Some content about Laravel.',
            'author' => 'Author Name',
            'source' => 'News API',
            'category_id' => $category->id,
            'unique_identifier' => Str::uuid(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/articles/' . $article->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['title' => 'Welcome to Laravel']);
    }

    public function test_show_article_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/articles/99999');
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['status' => false, 'message' => 'Article not found']);
    }

    //personalized user field api
    public function test_personalized_feed_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $categoryA = Category::create(['name' => 'Category A']);
        $categoryB = Category::create(['name' => 'Category B']);
        $preference = Preference::create([
            'user_id' => $user->id,
            'categories' => json_encode([1, 2]),
            'sources' => json_encode(['Source A', 'Source B']),
            'authors' => json_encode(['Author A', 'Author B']),
        ]);

        Article::create([
            'title' => 'Article from Source A',
            'content' => 'Content for article from Source A.',
            'author' => 'Author A',
            'source' => 'Source A',
            'categories' => json_encode([$categoryA->id, $categoryB->id]),
            'unique_identifier' => Str::uuid(),
        ]);

        Article::create([
            'title' => 'Article from Source B',
            'content' => 'Content for article from Source B.',
            'author' => 'Author B',
            'source' => 'Source B',
            'category_id' => $categoryA->id,
            'unique_identifier' => Str::uuid(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user-personalized-feed/' . $user->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Personalized news feed retrieved successfully.'])
            ->assertJsonCount(2, 'data.data');
    }

    public function test_personalized_feed_user_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user-personalized-feed/999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'User not found.']);
    }

    public function test_personalized_feed_preferences_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/user-personalized-feed/' . $user->id);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'User preferences not found.']);
    }
}
