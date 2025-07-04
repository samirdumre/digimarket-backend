<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the role seeder first
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create test user without factory (production-safe)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');

        // Create some sample categories
        $this->createCategories();

        // Create Passport client
        Artisan::call('passport:client', [
            '--personal' => true,
            '--no-interaction' => true,
        ]);
    }

    private function createCategories()
    {
        $categories = [
            [
                'name' => 'Software',
                'description' => 'Software applications and tools',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Graphics',
                'description' => 'Design resources and graphics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Templates',
                'description' => 'Website and document templates',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Audio',
                'description' => 'Music and audio files',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Video',
                'description' => 'Video content and tutorials',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}