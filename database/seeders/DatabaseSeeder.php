<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Kumar Shaikat Bala',
            'email' => 'kumarshaikatbala@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        DB::table('products')->insert([
            'name' => 'Laravel Book',
            'description' => 'A comprehensive guide to Laravel.',
            'price' => 50.00,
            'stock' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'name' => 'React Book',
            'description' => 'A comprehensive guide to React.',
            'price' => 60.00,
            'stock' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'name' => 'Vue Book',
            'description' => 'A comprehensive guide to Vue.',
            'price' => 70.00,
            'stock' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'name' => 'Django Book',
            'description' => 'A comprehensive guide to Django.',
            'price' => 80.00,
            'stock' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ordersData = [
            [
                'products' => [
                    ['product_id' => 1, 'quantity' => 2],
                    ['product_id' => 2, 'quantity' => 1],
                ],
            ],
            // Add more orders as needed
        ];

        DB::beginTransaction();
        try {
            foreach ($ordersData as $orderData) {
                $orders = [];
                $productUpdates = [];
                foreach ($orderData['products'] as $productData) {
                    $product = Product::findOrFail($productData['product_id']);
                    if ($product->stock < $productData['quantity']) {
                        throw new \Exception('Not enough stock for product ID ' . $productData['product_id']);
                    }
                    $total_price = $product->price * $productData['quantity'];
                    $orders[] = [
                        'user_id' => 1, // Replace with appropriate user ID
                        'product_id' => $productData['product_id'],
                        'quantity' => $productData['quantity'],
                        'total_price' => $total_price,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $productUpdates[] = [
                        'id' => $productData['product_id'],
                        'stock' => $product->stock - $productData['quantity'],
                    ];
                }

                Order::insert($orders);
                foreach ($productUpdates as $update) {
                    Product::where('id', $update['id'])->update(['stock' => $update['stock']]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }



    }
}
