<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Genre;
use App\Models\Actor;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir slug a la tabla genres
        Schema::table('genres', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Generar slugs para los géneros existentes
        Genre::all()->each(function ($genre) {
            $genre->slug = Str::slug($genre->name);
            $genre->save();
        });

        // Hacer el slug único y requerido después de generar los slugs
        Schema::table('genres', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });

        // Añadir slug a la tabla actors
        Schema::table('actors', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Generar slugs para los actores existentes
        Actor::all()->each(function ($actor) {
            $actor->slug = Str::slug($actor->name);
            $actor->save();
        });

        // Hacer el slug único y requerido después de generar los slugs
        Schema::table('actors', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('actors', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};