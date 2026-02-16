<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Get or Create Buyer
        $buyer = User::whereIn('role', ['buyer', 'customer'])->first();
        if (!$buyer) {
            $buyer = User::create([
                'name' => 'Test Buyer',
                'email' => 'buyer@test.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'status' => 'active'
            ]);
        }

        // Get or Create Seller
        $seller = User::where('role', 'seller')->first();
        if (!$seller) {
            $seller = User::create([
                'name' => 'Test Seller',
                'email' => 'seller@test.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'status' => 'active'
            ]);
        }

        // Get or Create Product
        $product = Product::first();
        if (!$product) {
            $product = Product::create([
                'user_id' => $seller->id,
                'name' => 'Test Product',
                'description' => 'Description',
                'price' => 100,
                'status' => 'approved',
                'image' => 'default.png'
            ]);
        }

        // Create Order
        Order::create([
            'user_id' => $buyer->id,
            'items' => [
                $product->id => [
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => $product->price,
                    'image' => $product->image
                ]
            ],
            'total_price' => $product->price,
            'status' => 'pending'
        ]);

        $this->command->info('Test Order Created Successfully');
    }
}
