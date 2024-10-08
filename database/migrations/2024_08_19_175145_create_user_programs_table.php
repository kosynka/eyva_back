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
        Schema::create('user_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->date('expiration_date')->nullable();
            $table->unsignedTinyInteger('status')->default(1);

            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('old_title');
            $table->text('old_description')->nullable();
            $table->text('old_requirements')->nullable();
            $table->unsignedInteger('old_duration_in_days');
            $table->unsignedInteger('old_price');
            $table->jsonb('photos')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_programs');
    }
};
