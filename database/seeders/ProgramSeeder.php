<?php

namespace Database\Seeders;


use App\Enums\BuyableStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        DB::table('programs')->insert([
            [
                'title' => 'Идеальная фигура',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Подтяжка лица',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Йога для омоложения',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Духовная преображение',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Anti-Aging Program',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Здоровая кожа',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Подтяжка бровей',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Гармония разума и тела',
                'description' => '',
                'requirements' => null,
                'duration_in_days' => 30,
                'price' => 1000,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
