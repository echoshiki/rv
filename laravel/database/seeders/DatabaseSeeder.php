<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ArticleCategorySeeder::class,
            ArticleSeeder::class, 
            MenuGroupSeeder::class
        ]);

        if (app()->environment('local')) {
            $this->call([
                ActivityCategorySeeder::class,
                ActivitySeeder::class,
                ActivityRegistrationSeeder::class,
            ]);
        }
    }
}
