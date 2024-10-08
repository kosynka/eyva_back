<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_service_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('type');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_schedule_id')->nullable()->constrained('service_schedules')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('user_program_service_id')->nullable()->constrained('user_program_services')->nullOnDelete();
            $table->foreignId('user_abonnement_id')->nullable()->constrained('user_abonnements')->nullOnDelete();
            $table->unsignedSmallInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_service_schedules');
    }
};
