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
            'rooms',
            function (Blueprint $table) {
                $table->id();
                $table->string('name'); // "Sala 1 IMAX", "Sala 2 VIP", etc.
                $table->string('type'); // "imax", "vip", "standard", etc.
                $table->integer('rows'); // número de filas (A-Z)
                $table->integer('seats_per_row'); // asientos por fila
                $table->decimal('price', 10, 2); // precio base de la sala
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

