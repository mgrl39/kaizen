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
            'functions',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('movie_id')->constrained()->onDelete('cascade');
                $table->foreignId('room_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->time('time');
                $table->boolean('is_3d')->default(false);
                $table->timestamps();

                // Una sala solo puede tener una película a la misma hora
                $table->unique(['room_id', 'date', 'time']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('functions');
    }
};

