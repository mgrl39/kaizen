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
	    Schema::create('admin_user', function (Blueprint $table) {
		    $table->id();
		    $table->string('email');
		    $table->string('username');
		    $table->string('admin_level');
		    $table->string('password');
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
