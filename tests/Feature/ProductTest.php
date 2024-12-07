<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_a_product()
    {

    }

    /** @test */
    public function a_user_cannot_create_a_product()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'Laravel Book',
            'description' => 'A book about Laravel',
            'price' => 50.00,
            'stock' => 10,
        ]);

        $response->assertStatus(403);
    }
}
