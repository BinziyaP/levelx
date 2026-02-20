<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use App\Models\Product;
use App\Models\ReturnLog;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DisputeTestSeeder extends Seeder
{
    public function run()
    {
        // 1. Get or Create a Buyer
        $buyer = User::where('role', 'customer')->first() ?? User::create([
            'name' => 'Test Buyer',
            'email' => 'buyer' . rand(1, 999) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);
        
        // 2. Get or Create a Seller
        $seller = User::where('role', 'seller')->first() ?? User::create([
            'name' => 'Test Seller',
            'email' => 'seller' . rand(1, 999) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'seller',
        ]);

        // 3. Get or Create a Category
        $category = Category::first() ?? Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        // 4. Create a Product for the seller
        $product = Product::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Dispute Test Product ' . Str::random(5),
            'brand' => 'TestBrand',
            'description' => 'A product for testing the dispute system.',
            'price' => 1000,
            'quantity' => 100,
            'status' => 'approved',
            'total_sales' => 10,
            'ranking_score' => 50
        ]);

        // 5. Create a Mock Order
        $order = Order::create([
            'user_id' => $buyer->id,
            'total_price' => 1000,
            'status' => 'paid',
            'shipping_status' => 'delivered',
            'items' => json_encode([
                [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => 1000
                ]
            ]),
            'payment_id' => 'pay_mock_' . rand(1000, 9999),
        ]);

        // 6. Create the Dispute (OrderReturn)
        $dispute = OrderReturn::create([
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'status' => 'pending',
            'reason' => 'Product arrived with significant cosmetic damage.',
            'refund_amount' => 0,
        ]);

        // 7. Log Initial Entry
        ReturnLog::create([
            'order_return_id' => $dispute->id,
            'old_status' => null,
            'new_status' => 'pending',
            'changed_by' => $buyer->id,
            'note' => 'Dispute raised by buyer via testing seeder.',
        ]);

        // 8. Add a Seller Response
        ReturnLog::create([
            'order_return_id' => $dispute->id,
            'old_status' => 'pending',
            'new_status' => 'pending',
            'changed_by' => $seller->id,
            'note' => 'I packed this very carefully. Please provide photos of the packaging.',
        ]);

        echo "SUCCESS: Test dispute #{$dispute->id} created for Order #{$order->id}.\n";
        echo "Buyer: {$buyer->email}\n";
        echo "Seller: {$seller->email}\n";
    }
}
