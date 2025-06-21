<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user first
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional users
        $users = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => bcrypt('password'), 'email_verified_at' => now()],
            ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'password' => bcrypt('password'), 'email_verified_at' => now()],
            ['name' => 'Mike Johnson', 'email' => 'mike.johnson@example.com', 'password' => bcrypt('password'), 'email_verified_at' => now()],
            ['name' => 'Sarah Wilson', 'email' => 'sarah.wilson@example.com', 'password' => bcrypt('password'), 'email_verified_at' => now()],
            ['name' => 'David Brown', 'email' => 'david.brown@example.com', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->assignRole('user');
        }

        // Create categories
        $categories = [
            ['name' => 'Web Templates', 'description' => 'Complete website templates and themes for various industries'],
            ['name' => 'Mobile Apps', 'description' => 'Source code and templates for mobile applications'],
            ['name' => 'Graphics & Design', 'description' => 'Digital graphics, logos, and design assets'],
            ['name' => 'E-books & Courses', 'description' => 'Educational content and digital learning materials'],
            ['name' => 'Plugins & Extensions', 'description' => 'Add-ons and extensions for various platforms']
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create products
        $products = [
            [
                'category_id' => 1,
                'title' => 'Modern E-commerce Website Template',
                'short_description' => 'Complete e-commerce template with shopping cart and payment integration',
                'description' => 'A fully responsive e-commerce website template built with HTML5, CSS3, and JavaScript. Features include product catalog, shopping cart, user authentication, payment integration, and admin dashboard. Perfect for online stores selling digital or physical products.',
                'price' => 79.99,
                'quantity' => 25,
                'thumbnail' => 'https://example.com/images/ecommerce-template-thumb.jpg',
                'images' => ["https://example.com/images/template-homepage.jpg","https://example.com/images/template-product-page.jpg","https://example.com/images/template-cart.jpg","https://example.com/images/template-admin.jpg"],
                'status' => 'approved',
                'download_count' => 0
            ],
            [
                'category_id' => 2,
                'title' => 'React Native Food Delivery App',
                'short_description' => 'Full-featured food delivery mobile app with real-time tracking',
                'description' => 'Complete React Native application for food delivery service. Includes customer app, restaurant dashboard, and delivery driver interface. Features real-time order tracking, payment processing, push notifications, and user reviews system.',
                'price' => 149.99,
                'quantity' => 15,
                'thumbnail' => 'https://example.com/images/food-app-thumb.jpg',
                'images' => ["https://example.com/images/app-home.jpg","https://example.com/images/app-menu.jpg","https://example.com/images/app-checkout.jpg","https://example.com/images/app-tracking.jpg"],
                'status' => 'approved',
                'download_count' => 0
            ],
            [
                'category_id' => 3,
                'title' => 'Premium Logo Design Collection',
                'short_description' => 'Professional logo templates for various business types',
                'description' => 'A comprehensive collection of 50+ professional logo designs in vector format. Includes logos for tech companies, restaurants, fashion brands, real estate, and more. All logos are fully editable and come with brand guidelines and color variations.',
                'price' => 39.99,
                'quantity' => 50,
                'thumbnail' => 'https://example.com/images/logo-collection-thumb.jpg',
                'images' => ["https://example.com/images/logo-tech.jpg","https://example.com/images/logo-restaurant.jpg","https://example.com/images/logo-fashion.jpg","https://example.com/images/logo-realestate.jpg"],
                'status' => 'approved',
                'download_count' => 0
            ],
            [
                'category_id' => 4,
                'title' => 'Complete Digital Marketing Course',
                'short_description' => 'Comprehensive course on modern digital marketing strategies',
                'description' => 'Master digital marketing with this complete course covering SEO, social media marketing, email marketing, content marketing, PPC advertising, and analytics. Includes 20+ hours of video content, practical exercises, templates, and lifetime updates.',
                'price' => 99.99,
                'quantity' => 100,
                'thumbnail' => 'https://example.com/images/marketing-course-thumb.jpg',
                'images' => ["https://example.com/images/course-seo.jpg","https://example.com/images/course-social.jpg","https://example.com/images/course-email.jpg","https://example.com/images/course-analytics.jpg"],
                'status' => 'approved',
                'download_count' => 0
            ],
            [
                'category_id' => 5,
                'title' => 'WordPress SEO Plugin Pro',
                'short_description' => 'Advanced SEO plugin for WordPress websites',
                'description' => 'Professional WordPress plugin to optimize your website for search engines. Features include XML sitemaps, meta tag optimization, schema markup, internal linking suggestions, and detailed SEO analysis. Compatible with all major WordPress themes and plugins.',
                'price' => 59.99,
                'quantity' => 200,
                'thumbnail' => 'https://example.com/images/seo-plugin-thumb.jpg',
                'images' => ["https://example.com/images/plugin-dashboard.jpg","https://example.com/images/plugin-analysis.jpg","https://example.com/images/plugin-settings.jpg","https://example.com/images/plugin-reports.jpg"],
                'status' => 'approved',
                'download_count' => 0
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create orders
        $orders = [
            [
                'buyer_id' => 2, // John Doe
                'order_number' => '550e8400-e29b-41d4-a716-446655440000',
                'total_amount' => 149.98,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'billing_email' => 'john.doe@example.com',
                'billing_name' => 'John Doe',
                'billing_address' => '123 Main Street, New York, NY 10001, USA'
            ],
            [
                'buyer_id' => 3, // Jane Smith
                'order_number' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'total_amount' => 79.99,
                'status' => 'processing',
                'payment_status' => 'paid',
                'payment_method' => 'paypal',
                'billing_email' => 'jane.smith@example.com',
                'billing_name' => 'Jane Smith',
                'billing_address' => '456 Oak Avenue, Los Angeles, CA 90210, USA'
            ],
            [
                'buyer_id' => 4, // Mike Johnson
                'order_number' => '6ba7b811-9dad-11d1-80b4-00c04fd430c8',
                'total_amount' => 299.97,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'bank_transfer',
                'billing_email' => 'mike.johnson@example.com',
                'billing_name' => 'Mike Johnson',
                'billing_address' => '789 Pine Street, Chicago, IL 60601, USA'
            ],
            [
                'buyer_id' => 5, // Sarah Wilson
                'order_number' => '6ba7b812-9dad-11d1-80b4-00c04fd430c8',
                'total_amount' => 49.99,
                'status' => 'cancelled',
                'payment_status' => 'refunded',
                'payment_method' => 'credit_card',
                'billing_email' => 'sarah.wilson@example.com',
                'billing_name' => 'Sarah Wilson',
                'billing_address' => '321 Elm Drive, Houston, TX 77001, USA'
            ],
            [
                'buyer_id' => 6, // David Brown
                'order_number' => '6ba7b813-9dad-11d1-80b4-00c04fd430c8',
                'total_amount' => 199.98,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'billing_email' => 'david.brown@example.com',
                'billing_name' => 'David Brown',
                'billing_address' => '654 Maple Lane, Phoenix, AZ 85001, USA'
            ]
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }

        // Create Passport client
        Artisan::call('passport:client', [
            '--personal' => true,
            '--no-interaction' => true,
        ]);
    }
}
