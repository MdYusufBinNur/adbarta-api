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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('uid')->unique();
            $table->string('photo')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('website')->nullable();
            $table->string('company')->nullable();
            $table->longText('about')->nullable();
            $table->string('role')->default('seller');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('sub_district_id')->nullable();
            $table->dateTime('last_activity')->default(now()->format('Y-m-d H:i:s'));
            $table->boolean('active')->default(0);
            $table->enum('status',['pending','requested','approved','declined'])->default('pending');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
