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
            ArticleCategorySeeder::class,
            ActivityCategorySeeder::class,
            ArticleSeeder::class, 
            MenuGroupSeeder::class,
            RvCategorySeeder::class
        ]);

        if (app()->environment('local')) {
            $this->call([
                UserSeeder::class,
                ActivitySeeder::class,
                ActivityRegistrationSeeder::class,
                BannerSeeder::class,
                UsedRvSeeder::class,
                MyCarSeeder::class,
                RvOrderSeeder::class,
                RvSeeder::class,
                MaintenanceSeeder::class,
                SuggestSeeder::class,
            ]);
        }
    }
}
