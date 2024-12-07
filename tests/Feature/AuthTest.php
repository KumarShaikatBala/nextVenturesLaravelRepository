<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration functionality.
     *
     * @return void
     */
    public function test_a_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Adjust the expected message to match the actual API response
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User register successfully.', // Match the actual response
            ]);

        // Assert that the user is saved in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test user login functionality.
     *
     * @return void
     */
    public function test_a_user_can_login()
    {
        // Create a user in the database
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'), // Make sure the password is hashed
        ]);

        // Attempt to log in with the correct credentials
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        // Assert the login was successful and a token is returned
        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    /**
     * Test user cannot register with invalid data.
     *
     * @return void
     */
    public function test_a_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '', // Invalid name
            'email' => 'not-an-email', // Invalid email
            'password' => 'password',
            'password_confirmation' => 'not-matching', // Passwords do not match
        ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test user cannot login with incorrect credentials.
     *
     * @return void
     */
    public function test_a_user_cannot_login_with_invalid_credentials()
    {
        // Create a user in the database
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt to log in with incorrect credentials
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert the login failed
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }
}
