<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        DB::table('categories')->insert([
            [
                'type' => Category::TYPE_CATEGORY,
                'title' => 'Красота и здоровье',
                'description' => '',
                'parent_id' => null,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_CATEGORY,
                'title' => 'Танцы и фитнес',
                'description' => '',
                'parent_id' => null, 
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_CATEGORY,
                'title' => 'Духовность и осознанность',
                'description' => '',
                'parent_id' => null, 
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_CATEGORY,
                'title' => 'Соляная комната',
                'description' => '',
                'parent_id' => null, 
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Osteos Space',
                'description' => '',
                'parent_id' => 1,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Beauty & Taping Space',
                'description' => '',
                'parent_id' => 1,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Dance Fit Space',
                'description' => '',
                'parent_id' => 2,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Dance',
                'description' => '',
                'parent_id' => 2,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Fitness',
                'description' => '',
                'parent_id' => 2,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Soul Space',
                'description' => '',
                'parent_id' => 3,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Category::TYPE_ROUTE,
                'title' => 'Salt Space',
                'description' => '',
                'parent_id' => 4,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        CategoryService::factory()->count(50)->create();
    }
}
