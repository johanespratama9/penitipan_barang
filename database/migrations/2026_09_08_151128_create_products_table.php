<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('consignor_id')->constrained('consignors')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('consignor_price', 15, 2)->default(0);
            $table->string('commission_type')->default('percentage'); // percentage, fixed
            $table->decimal('commission_value', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('status')->default('pending'); // pending, available, sold, returned, rejected, expired
            $table->string('condition')->nullable(); // new, like_new, used, fair
            $table->string('image')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('code');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
