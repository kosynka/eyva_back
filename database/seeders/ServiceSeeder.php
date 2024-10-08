<?php

namespace Database\Seeders;

use App\Models\InstructorService;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        DB::table('services')->insert([
            [
                'type' => Service::TYPE_SERVICE,
                'title' => 'Belly Dance',
                'description' => '',
                'requirements' => '',
                'duration' => 90,
                'places_count' => 12,
                'complexity' => 'easy',
                'price' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Service::TYPE_SERVICE,
                'title' => 'Йога',
                'description' => '',
                'requirements' => '',
                'duration' => 55,
                'places_count' => 12,
                'complexity' => 'easy',
                'price' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Service::TYPE_SERVICE,
                'title' => 'Женское здоровье',
                'description' => '',
                'requirements' => '',
                'duration' => 55,
                'places_count' => 12,
                'complexity' => 'easy',
                'price' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Service::TYPE_SERVICE,
                'title' => 'Test',
                'description' => 'some text',
                'requirements' => '',
                'duration' => 30,
                'places_count' => 1,
                'complexity' => 'medium',
                'price' => 90,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Service::TYPE_MASTERCLASS,
                'title' => 'Test m 1',
                'description' => 'some text',
                'requirements' => '',
                'duration' => 30,
                'places_count' => 1,
                'complexity' => 'medium',
                'price' => 90,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => Service::TYPE_MASTERCLASS,
                'title' => 'Test m 2',
                'description' => 'some text',
                'requirements' => '',
                'duration' => 45,
                'places_count' => 1,
                'complexity' => 'hard',
                'price' => 90,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        InstructorService::factory()->count(30)->create();
    }
}
