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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->unsignedInteger('duration_in_days');
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE programs ADD COLUMN searchtext TSVECTOR");
        DB::statement("UPDATE programs SET searchtext = to_tsvector('russian', title || ' ' || description || ' ' || requirements)");
        DB::statement("CREATE INDEX programs_searchtext_gin ON programs USING GIN(searchtext)");
        DB::statement("CREATE TRIGGER ts_searchtext BEFORE INSERT OR UPDATE ON programs FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('searchtext', 'pg_catalog.russian', 'title', 'description', 'requirements')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS tsvector_update_trigger ON programs");
        DB::statement("DROP INDEX IF EXISTS programs_searchtext_gin");
        DB::statement("ALTER TABLE programs DROP COLUMN searchtext");

        Schema::dropIfExists('programs');
    }
};
