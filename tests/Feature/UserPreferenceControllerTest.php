<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_preferences_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $categoryA = Category::create(['name' => 'Category A']);
        $categoryB = Category::create(['name' => 'Category B']);
        $preferencesData = [
            'user_id' => $user->id,
            'categories' => [$categoryA->id, $categoryB->id],
            'sources' => ['Source A', 'Source B'],
            'authors' => ['Author A', 'Author B'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/preferences', $preferencesData);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Success']);
    }

    public function test_set_preferences_invalid_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $categoryA = Category::create(['name' => 'Category A']);
        $categoryB = Category::create(['name' => 'Category B']);
        $preferencesData = [
            'user_id' => 9999,
            'categories' => [$categoryA->id, $categoryB->id],
            'sources' => ['Source A', 'Source B'],
            'authors' => ['Author A', 'Author B'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/preferences', $preferencesData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => 'The selected user id is invalid.']);
    }

    public function test_get_preferences_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $categoryA = Category::create(['name' => 'Category A']);
        $categoryB = Category::create(['name' => 'Category B']);

        $preferencesData = [
            'user_id' => $user->id,
            'categories' => [$categoryA->id, $categoryB->id],
            'sources' => ['Source A', 'Source B'],
            'authors' => ['Author A', 'Author B'],
        ];
        Preference::create([
            'user_id' => $user->id,
            'categories' => json_encode($preferencesData['categories']),
            'sources' => json_encode($preferencesData['sources']),
            'authors' => json_encode($preferencesData['authors']),
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/preferences/' . $user->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['user_id' => $user->id])
            ->assertJsonFragment(['sources' => ['Source A', 'Source B']])
            ->assertJsonFragment(['authors' => ['Author A', 'Author B']]);
    }

    public function test_get_preferences_user_not_found()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/preferences/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment(['message' => 'User not found.']);
    }

    public function test_get_preferences_no_preferences_found()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/preferences/' . $user->id);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment(['message' => 'Preferences not found for the given user.']);
    }
}
