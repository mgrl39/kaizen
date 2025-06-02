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
                $table->foreignId('function_id')->constrained()->onDelete('cascade');
                $table->string('row'); // A, B, C, etc.
                $table->integer('number'); // 1, 2, 3, etc.
                $table->enum('status', ['free', 'booked'])->default('free');
                $table->timestamps();

                // Un asiento único por función
                $table->unique(['function_id', 'row', 'number']);
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

