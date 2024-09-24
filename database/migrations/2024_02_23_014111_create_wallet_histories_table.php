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
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_wallet_id')->constrained()->cascadeOnDelete();
            $table->double('points');
            $table->string('points_type'); //credit or debit
            $table->string('gateway'); //bkash, nagad, card
            $table->enum('status',['approved','not_approved','pending','cancelled'])->default('pending');
            $table->string('trxID')->unique();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('nid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_histories');
    }
};
