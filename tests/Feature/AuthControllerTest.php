<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // successfull registration
    public function test_successful_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, User::all());
        $this->assertEquals('Test User', User::first()->name);
        $this->assertTrue(Hash::check('Password@123', User::first()->password));
    }


    public function test_registration_missing_name()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'test@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name field is required.']);
    }


    public function test_registration_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The email field must be a valid email address.']);
    }

    public function test_registration_password_confirmation_mismatch()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The password field confirmation does not match.']);
    }

    public function test_registration_weak_password()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The password field must be at least 8 characters.']);
    }
    public function test_registration_duplicate_email()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'test@gmail.com',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'test@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The email has already been taken.']);
    }

    /** @test */
    /** @test */
    public function user_can_login_successfully()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('Password@123'),
            'logout_time' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'message', 'data' => ['token']]);
    }

    /** @test */
    public function test_login_requires_email()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'Password@123',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The email field is required.']);
    }

    /** @test */
    public function test_login_requires_valid_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'abc',
            'password' => 'Password@123',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
             $response->assertJsonFragment(['The email field must be a valid email address.']);
    }

    /** @test */
    public function test_login_requires_existing_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'abc@gmail.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The selected email is invalid.',
            ]);
    }

    /** @test */
    public function test_login_requires_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@gmail.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The password field is required.',
            ]);
    }

    /** @test */
    public function test_login_with_incorrect_password()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('Password@123'),
            'logout_time' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@gmail.com',
            'password' => 'WrongPassword@123',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['status' => false, 'message' => 'Invalid Credentials']);
    }

    /** @test */
    public function test_login_when_user_already_logged_in()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('Password@123'),
            'logout_time' => null,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@gmail.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
            ->assertJson(['status' => false, 'message' => 'User Already Logged-in']);
    }

    //forgot password api test
    public function test_forgot_password_with_registered_email()
    {
        $user = User::factory()->create(['email' => 'testuser@gmail.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'We have emailed your password reset link.',
            ]);
    }

    public function test_forgot_password_with_unregistered_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'notregistered@gmail.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The selected email is invalid.',
            ]);
    }

    public function test_forgot_password_with_invalid_email_format()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'abc',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The email field must be a valid email address.',
            ]);
    }

    // test rest password
    public function test_reset_password_successfully()
    {
        $user = User::factory()->create(['email' => 'testuser@gmail.com']);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword@123',
            'password_confirmation' => 'NewPassword@123',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Your password has been reset.',
            ]);
    }

    public function test_reset_password_with_invalid_token()
    {
        $user = User::factory()->create(['email' => 'testuser@gmail.com']);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid_token',
            'email' => $user->email,
            'password' => 'NewPassword@123',
            'password_confirmation' => 'NewPassword@123',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'status' => false,
                'message' => 'This password reset token is invalid.',
            ]);
    }

    public function test_reset_password_with_non_matching_passwords()
    {
        $user = User::factory()->create(['email' => 'testuser@gmail.com']);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword@123',
            'password_confirmation' => 'abc@123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The password field confirmation does not match.',
            ]);
    }

    //logout api test
    public function test_successful_logout()
    {
        $user = User::factory()->create(['email' => 'testuser@gmail.com']);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->postJson('/api/logout', [
            'email' => $user->email,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Access Token Deleted',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'logout_time' => now(),
        ]);

        $this->assertTrue($user->tokens()->count() === 0);
    }

    public function test_logout_with_unregistered_email()
    {
        $response = $this->postJson('/api/logout', [
            'email' => 'abc@gmail.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The selected email is invalid.',
            ]);
    }

    public function test_logout_requires_email()
    {
        $response = $this->postJson('/api/logout', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The email field is required.',
            ]);
    }

    public function test_logout_with_invalid_email_format()
    {
        $response = $this->postJson('/api/logout', [
            'email' => 'abc',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'message' => 'The email field must be a valid email address.',
            ]);
    }

}
