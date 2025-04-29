<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Movie;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Generar slugs para las películas existentes
        Movie::all()->each(function ($movie) {
            $movie->slug = Str::slug($movie->title);
            $movie->save();
        });

        // Hacer el slug único y requerido después de generar los slugs
        Schema::table('movies', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
