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
        Schema::create('user_abonnement_presents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_abonnement_id')->constrained('user_abonnements')->cascadeOnDelete();
            $table->unsignedInteger('visits');
            $table->foreignId('abonnement_present_id')->nullable()->constrained('abonnement_presents')->nullOnDelete();
            $table->string('old_text')->nullable();
            $table->unsignedInteger('old_visits');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_abonnement_presents');
    }
};
