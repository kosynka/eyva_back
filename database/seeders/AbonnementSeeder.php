<?php

namespace Database\Seeders;

use App\Enums\BuyableStatusEnum;
use App\Models\AbonnementPresent;
use DB;
use Illuminate\Database\Seeder;

class AbonnementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        DB::table('abonnements')->insert([
            [
                'title' => '1 месяц',
                'duration_in_days' => 30,
                'minutes' => 450,
                'price' => 75,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '1 месяц',
                'duration_in_days' => 30,
                'minutes' => 666,
                'price' => 111,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '3 месяца',
                'duration_in_days' => 91,
                'minutes' => 1350,
                'price' => 225,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '3 месяца',
                'duration_in_days' => 91,
                'minutes' => 1998,
                'price' => 333,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '6 месяцев',
                'duration_in_days' => 182,
                'minutes' => 2700,
                'price' => 450,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '12 месяцев',
                'duration_in_days' => 365,
                'minutes' => 5400,
                'price' => 900,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => '12 месяцев',
                'duration_in_days' => 365,
                'minutes' => 7998,
                'price' => 1333,
                'status' => BuyableStatusEnum::DISABLED_KEY,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        AbonnementPresent::factory()->count(30)->create();
    }
}
