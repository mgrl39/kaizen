<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'movies',
            function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('synopsis')->nullable();
                $table->integer('duration');
                $table->string('rating')->nullable();
                $table->date('release_date')->nullable();
                $table->string('photo_url')->nullable();
                $table->string('original_image_path')->nullable();
                $table->string('directors')->nullable();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
}; 