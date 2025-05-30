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
            'manages',
            function (Blueprint $table) {
                $table->foreignId('admin_id')->constrained('users');
                $table->foreignId('cinema_id')->constrained();
                $table->primary(['admin_id', 'cinema_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manages');
    }
};

