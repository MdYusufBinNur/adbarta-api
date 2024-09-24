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
        Schema::create('bkash_token_managers', function (Blueprint $table) {
            $table->id();
            $table->string('id_token');
            $table->string('token_type');
            $table->string('expires_in');
            $table->string('statusCode');
            $table->string('statusMessage');
            $table->string('refresh_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkash_token_managers');
    }
};
