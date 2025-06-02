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
            'bookings',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('function_id')->constrained()->onDelete('cascade');
                $table->foreignId('seat_id')->constrained()->onDelete('cascade');
                $table->string('code')->unique(); // código de reserva
                $table->decimal('price', 10, 2); // precio final pagado
                $table->boolean('paid')->default(false);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

