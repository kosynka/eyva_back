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
        Schema::create('user_program_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_program_id')->constrained('user_programs')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->nullOnDelete();
            $table->unsignedInteger('visits');
            $table->foreignId('program_service_id')->nullable()->constrained('program_services')->nullOnDelete();
            $table->unsignedInteger('old_visits');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_program_services');
    }
};
