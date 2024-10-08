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
        Schema::create('abonnement_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('abonnement_id')
                ->nullable()
                ->constrained('abonnements')
                ->nullOnDelete();

            $table->unsignedSmallInteger('type')->default(1);
            $table->string('link');
            $table->string('preview')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnement_photos');
    }
};