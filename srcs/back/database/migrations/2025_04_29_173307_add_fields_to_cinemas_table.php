<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('cinemas', function (Blueprint $table) {
            $table->string('address')->nullable()->after('location');
            $table->string('phone')->nullable()->after('address');
            $table->string('image_url')->nullable()->after('phone');
            $table->integer('rooms_count')->nullable()->after('image_url');
            $table->boolean('has_3d')->default(false)->after('rooms_count');
            $table->boolean('has_imax')->default(false)->after('has_3d');
            $table->boolean('has_vip')->default(false)->after('has_imax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cinemas', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'phone', 'image_url', 'rooms_count',
                'has_3d', 'has_imax', 'has_vip'
            ]);
        });
    }
};
