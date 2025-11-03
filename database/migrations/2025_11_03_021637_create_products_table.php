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
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->timestamps();
            
            $t->string('sku')->unique();
            $t->string('name');
            $t->string('brand')->index();
            $t->string('category')->index();
            $t->text('description');
            $t->decimal('price', 10, 2);
            $t->string('currency', 3)->default('USD');
            $t->unsignedInteger('stock')->default(0);
            $t->string('url');
            $t->json('attributes')->nullable();
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
