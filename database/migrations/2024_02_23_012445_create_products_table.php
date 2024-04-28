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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('sub_category_id')->constrained();
            $table->longText('slug')->nullable();
            $table->string('title')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->longText('location')->nullable();
            $table->string('condition')->nullable();
            $table->string('brand')->nullable();
            $table->string('edition')->nullable();
            $table->enum('product_type',['normal','premium'])->default('normal');
            $table->integer('priority')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->string('authenticity')->nullable();
            $table->longText('features')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('sub_district_id')->nullable();
            $table->bigInteger('view')->default(1);
            $table->enum('status',['pending','approved','sold','not_approved'])->default('pending');
            $table->double('points')->nullable();
            $table->double('price')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('additional_contact_number')->nullable();
            $table->string('show_contact_number')->nullable();
            $table->boolean('accept_terms')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
