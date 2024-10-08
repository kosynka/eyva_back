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
        Schema::create('service_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('hall')->nullable();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->date('start_date');
            $table->time('start_time');
            $table->unsignedInteger('places_count_total');
            $table->unsignedInteger('places_count_left');
            $table->string('complexity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_schedules');
    }
};
