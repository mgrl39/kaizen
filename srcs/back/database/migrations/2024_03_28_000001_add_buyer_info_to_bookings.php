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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'buyer_name')) {
                $table->string('buyer_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'buyer_email')) {
                $table->string('buyer_email')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'buyer_phone')) {
                $table->string('buyer_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_email', 'buyer_phone']);
        });
    }
}; 