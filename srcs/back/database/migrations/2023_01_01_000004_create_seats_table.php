<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Seat;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('function_id')->constrained()->onDelete('cascade');
            $table->string('row'); // A, B, C, etc.
            $table->integer('number'); // 1, 2, 3, etc.
            $table->enum('status', [Seat::STATUS_AVAILABLE, Seat::STATUS_RESERVED, Seat::STATUS_OCCUPIED])->default(Seat::STATUS_AVAILABLE);
            $table->decimal('price', 8, 2);
            $table->timestamps();

            // Un asiento único por función
            $table->unique(['function_id', 'row', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};

