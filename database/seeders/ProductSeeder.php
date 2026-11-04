<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Electronics & Gadgets
            ['name' => 'Wireless Bluetooth Headphones', 'price' => 1999.00, 'stock' => 25],
            ['name' => 'Mechanical Gaming Keyboard', 'price' => 3499.00, 'stock' => 15],
            ['name' => 'Ergonomic Wireless Mouse', 'price' => 899.00, 'stock' => 40],
            ['name' => 'Type-C Fast Charger (65W)', 'price' => 1299.00, 'stock' => 30],
            ['name' => 'Smart Fitness Band', 'price' => 2499.00, 'stock' => 20],
            ['name' => '10000mAh Power Bank', 'price' => 1199.00, 'stock' => 50],
            ['name' => 'Bluetooth Portable Speaker', 'price' => 1799.00, 'stock' => 18],
            ['name' => 'HD Webcam with Mic', 'price' => 2199.00, 'stock' => 12],
            ['name' => 'RGB Gaming Headset', 'price' => 2899.00, 'stock' => 22],
            ['name' => 'Laptop Stand Foldable', 'price' => 799.00, 'stock' => 35],
            ['name' => 'USB-C Hub Multiport Adapter', 'price' => 1599.00, 'stock' => 28],
            ['name' => 'Smart LED Bulb (Wi-Fi)', 'price' => 599.00, 'stock' => 60],
            ['name' => 'Noise Cancelling Earbuds', 'price' => 3999.00, 'stock' => 14],
            ['name' => 'External SSD 512GB', 'price' => 4999.00, 'stock' => 10],
            ['name' => 'Ring Light with Tripod', 'price' => 1499.00, 'stock' => 25],

            // Wearables & Apparel
            ['name' => 'Classic Analog Watch', 'price' => 2999.00, 'stock' => 20],
            ['name' => 'Cotton Casual T-Shirt', 'price' => 499.00, 'stock' => 100],
            ['name' => 'Slim Fit Denim Jeans', 'price' => 1499.00, 'stock' => 45],
            ['name' => 'Waterproof Sports Jacket', 'price' => 2499.00, 'stock' => 18],
            ['name' => 'Running Sports Shoes', 'price' => 1999.00, 'stock' => 30],
            ['name' => 'Leather Wallet for Men', 'price' => 699.00, 'stock' => 50],
            ['name' => 'Canvas Casual Sneakers', 'price' => 1299.00, 'stock' => 35],
            ['name' => 'Polarized Sunglasses', 'price' => 999.00, 'stock' => 40],
            ['name' => 'Cotton Hooded Sweatshirt', 'price' => 1299.00, 'stock' => 25],
            ['name' => 'Formal Button-Down Shirt', 'price' => 899.00, 'stock' => 40],

            // Home & Kitchen
            ['name' => 'Stainless Steel Water Bottle', 'price' => 699.00, 'stock' => 75],
            ['name' => 'Insulated Coffee Tumbler', 'price' => 899.00, 'stock' => 50],
            ['name' => 'Electric Kettle 1.5L', 'price' => 1299.00, 'stock' => 20],
            ['name' => 'Non-Stick Frying Pan', 'price' => 999.00, 'stock' => 30],
            ['name' => 'Air Tight Storage Containers (Set of 6)', 'price' => 799.00, 'stock' => 40],
            ['name' => 'BPA-Free Lunch Box', 'price' => 549.00, 'stock' => 60],
            ['name' => 'Microfiber Cleaning Cloths (Pack of 4)', 'price' => 299.00, 'stock' => 120],
            ['name' => 'LED Desk Lamp with Dimmer', 'price' => 1199.00, 'stock' => 22],
            ['name' => 'Memory Foam Pillow', 'price' => 1499.00, 'stock' => 15],
            ['name' => 'Cotton Bedsheet Set (Queen Size)', 'price' => 1799.00, 'stock' => 18],

            // Fitness & Outdoors
            ['name' => 'Yoga Mat with Carry Strap', 'price' => 799.00, 'stock' => 45],
            ['name' => 'Adjustable Hand Gripper', 'price' => 349.00, 'stock' => 80],
            ['name' => 'Resistance Workout Bands Set', 'price' => 599.00, 'stock' => 55],
            ['name' => 'Skipping Jump Rope (Steel Wire)', 'price' => 299.00, 'stock' => 90],
            ['name' => 'Dumbbell Pair (5kg each)', 'price' => 2299.00, 'stock' => 10],
            ['name' => 'Camping Backpack 40L', 'price' => 2499.00, 'stock' => 16],
            ['name' => 'Microfiber Travel Towel', 'price' => 499.00, 'stock' => 40],
            ['name' => 'Push-Up Bars Stand', 'price' => 699.00, 'stock' => 35],
            ['name' => 'Foam Roller for Muscle Massage', 'price' => 899.00, 'stock' => 28],
            ['name' => 'Sports Armband for Smartphone', 'price' => 399.00, 'stock' => 65],

            // Stationery & Accessories
            ['name' => 'A5 Ruled Notebook Journal', 'price' => 249.00, 'stock' => 150],
            ['name' => 'Gel Pen Set (Pack of 10)', 'price' => 199.00, 'stock' => 200],
            ['name' => 'Desk Organizer Caddy', 'price' => 499.00, 'stock' => 45],
            ['name' => 'Sticky Notes Mega Pack', 'price' => 179.00, 'stock' => 130],
            ['name' => 'Metal Book Stand Holder', 'price' => 599.00, 'stock' => 40],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}