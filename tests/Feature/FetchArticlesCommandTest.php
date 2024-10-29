<?php
namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Carbon\Carbon;

class FetchArticlesCommandTest extends TestCase
{
    use RefreshDatabase;
    public function test_articles_fetch_command_stores_articles_correctly()
    {
        Cache::flush();
        $categories = Category::factory()->count(3)->create();

        Http::fake([
            'https://newsapi.org/*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Newsapi title a',
                        'description' => 'Description for NewsAPI article.',
                        'author' => 'Author a',
                        'source' => ['name' => 'Newsapi source a'],
                        'publishedAt' => now()->toIso8601String(),
                        'url' => 'https://newsapi.org/article-1',

                    ],
                ]
            ], 200)
        ]);


        Http::fake([
            'https://content.guardianapis.com/*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'id' => 'guardian-article-1',
                            'webTitle' => 'Guardian title b',
                            'sectionName' => 'Guardian Category',
                            'webPublicationDate' => now()->toIso8601String(),
                            'webUrl' => 'https://guardian.com/article-1',

                        ],
                    ]
                ]
            ], 200)
        ]);

            Http::fake([
            'https://api.nytimes.com/*' => Http::response([
                'results' => [
                    [
                        'title' => 'NYT title a',
                        'abstract' => 'Abstract for NYT article.',
                        'byline' => 'By Author B',
                        'section' => 'NYT Category',
                        'published_date' => now()->toIso8601String(),
                        'url' => 'https://nytimes.com/article-1',
                        'uri' => 'nyt-article-1',

                    ],
                ]
            ], 200)
        ]);

        Artisan::call('articles:fetch');

        $this->assertDatabaseHas('articles', [
            'title' => 'Newsapi title a',
            'source' => 'Newsapi source a',
            'author' => 'Author a',
            'category_id' => 1
        ]);

         $this->assertDatabaseHas('articles', [
            'title' => 'Guardian title b',
            'source' => 'The Guardian',
            'content' => 'https://guardian.com/article-1',
             'category_id' => 4
        ]);

         $this->assertDatabaseHas('articles', [
            'title' => 'NYT title a',
            'source' => 'The New York Times',
            'author' => 'Author B',
             'category_id' => 5
         ]);

        $articleCategoryIds = Article::all()->pluck('category_id')->unique()->values()->toArray();

        $this->assertEquals(
            [1, 4, 5],
            $articleCategoryIds
        );
        $this->assertTrue(true, 'Articles fetched and stored successfully.');
    }
}

