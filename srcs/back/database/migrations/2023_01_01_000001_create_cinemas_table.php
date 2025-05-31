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
            'cinemas',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('location');
                $table->string('address');
                $table->string('phone');
                $table->string('email');
                $table->text('description');
                $table->string('image_url');
                $table->boolean('has_3d')->default(false);
                $table->boolean('has_imax')->default(false);
                $table->boolean('has_vip')->default(false);
                $table->integer('rooms_count')->default(0);
                $table->string('opening_hours');
                $table->json('features')->nullable();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
};

