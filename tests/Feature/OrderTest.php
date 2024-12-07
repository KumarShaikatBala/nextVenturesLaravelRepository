<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_place_an_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Order placed successfully',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8, // Ensure stock is reduced
        ]);
    }

    /** @test */
    public function an_order_cannot_be_placed_if_stock_is_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Insufficient stock',
            ]);
    }
}
