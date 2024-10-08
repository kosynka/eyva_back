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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('type')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->unsignedInteger('duration');
            $table->unsignedInteger('places_count')->default(1);
            $table->string('complexity')->nullable();
            $table->unsignedInteger('price');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE services ADD COLUMN searchtext TSVECTOR");
        DB::statement("UPDATE services SET searchtext = to_tsvector('russian', title || ' ' || description || ' ' || requirements)");
        DB::statement("CREATE INDEX services_searchtext_gin ON services USING GIN(searchtext)");
        DB::statement("CREATE TRIGGER ts_searchtext BEFORE INSERT OR UPDATE ON services FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('searchtext', 'pg_catalog.russian', 'title', 'description', 'requirements')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS tsvector_update_trigger ON services");
        DB::statement("DROP INDEX IF EXISTS services_searchtext_gin");
        DB::statement("ALTER TABLE services DROP COLUMN searchtext");

        Schema::dropIfExists('services');
    }
};
