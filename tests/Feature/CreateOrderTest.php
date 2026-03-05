<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_successfully()
    {
        $shop = Shop::factory()->create();

        $product = Product::factory()->create([
            'price' => 100,
            'stock' => 10
        ]);

        $response = $this->postJson('/api/orders', [
            'shop_id' => $shop->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $shop->id
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
}