<?php

namespace Database\Seeders;

use App\Models\Abonnement;
use App\Models\Program;
use App\Models\ProgramService;
use App\Models\ServiceSchedule;
use App\Models\User;
use App\Models\UserAbonnement;
use App\Models\UserAbonnementPresent;
use App\Models\UserProgram;
use App\Models\UserProgramService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $password = \Hash::make('password');

        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@eyva.kz',
                'phone' => null,
                'employee_description' => null,
                'role' => User::ROLE_ADMIN,
                'password' => \Hash::make('admin'),
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sayat',
                'email' => 'sayat.kaldarbekov.00@gmail.kz',
                'phone' => '77022363206',
                'employee_description' => null,
                'role' => User::ROLE_USER,
                'password' => $password,
                'balance' => 100_000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Архипова Анна',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Грушина Ирина',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Гурбаналиева Алина',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Годованая Ксения',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Караганова Анель',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Биржанов Амир',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Воронина Кристина',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Алексеев Алексей',
                'email' => null,
                'phone' => null,
                'employee_description' => 'Косметолог',
                'role' => User::ROLE_EMPLOYEE,
                'password' => $password,
                'balance' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $this->call([
            ServiceSeeder::class,
            AbonnementSeeder::class,
            CategorySeeder::class,
            ProgramSeeder::class,
        ]);

        // ServiceSchedule::factory()->count(300)->create();
        // ProgramService::factory()->count(100)->create();

        $user = User::create([
            'name' => 'Daniyar',
            'email' => 'daniyar0@gmail.kz',
            'phone' => '77000000000',
            'role' => User::ROLE_USER,
            'password' => \Hash::make('myPa$sword123'),
            'balance' => 100_000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // $abonnement = Abonnement::first();

        // $userAbonnement = UserAbonnement::create([
        //     'user_id' => $user->id,
        //     'transaction_id' => null,
        //     'expiration_date' => now()->addDays($abonnement->duration_in_days)->toDateString(),
        //     'minutes' => $abonnement->minutes,
        //     'status' => UserAbonnement::STATUS_ACTIVE,

        //     'abonnement_id' => $abonnement->id,
        //     'old_title' => $abonnement->title,
        //     'old_duration_in_days' => $abonnement->duration_in_days,
        //     'old_minutes' => $abonnement->minutes,
        //     'old_price' => $abonnement->price,
        // ]);

        // foreach ($abonnement->presents as $abonnementPresent) {
        //     UserAbonnementPresent::create([
        //         'user_abonnement_id' => $userAbonnement->id,
        //         'visits' => $abonnementPresent->visits,
        //         'abonnement_present_id' => $abonnementPresent->id,
        //         'old_visits' => $abonnementPresent->visits,
        //     ]);
        // }

        // $program = Program::with(['programServices'])->first();

        // $userProgram = UserProgram::create([
        //     'user_id' => $user->id,
        //     'transaction_id' => null,
        //     'expiration_date' => now()->addDays($program->duration_in_days)->toDateString(),
        //     'status' => UserProgram::STATUS_ACTIVE,

        //     'program_id' => $program->id,
        //     'old_title' => $program->title,
        //     'old_description' => $program->description,
        //     'old_requirements' => $program->requirements,
        //     'old_duration_in_days' => $program->duration_in_days,
        //     'old_price' => $program->price,
        // ]);

        // foreach ($program->programServices as $programService) {
        //     UserProgramService::create([
        //         'user_program_id' => $userProgram->id,
        //         'service_id' => $programService->service_id,
        //         'visits' => $programService->visits,
        //         'program_service_id' => $programService->id,
        //         'old_visits' => $programService->visits,
        //     ]);
        // }
    }
}
