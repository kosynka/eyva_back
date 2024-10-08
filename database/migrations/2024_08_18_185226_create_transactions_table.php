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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('payment_service_id')->nullable();
            $table->unsignedSmallInteger('type');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('amount_in_currency')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedSmallInteger('status')->default(0);
            $table->text('comment')->nullable();

            $table->string('related_with')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
