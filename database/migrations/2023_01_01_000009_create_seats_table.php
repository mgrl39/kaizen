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
            'seats',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('function_id')->constrained();
                $table->integer('number');
                $table->string('seat_row', 10);
                $table->enum('status', ['available', 'reserved', 'occupied']);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};

