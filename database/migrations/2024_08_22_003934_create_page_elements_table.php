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
        Schema::create('page_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('page_type');
            $table->string('key');
            $table->text('text')->nullable();
            $table->string('file')->nullable();
            $table->unsignedSmallInteger('file_mime_type')->default(1)->nullable();
            $table->string('preview')->nullable();
            $table->unsignedBigInteger('weight')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_elements');
    }
};
