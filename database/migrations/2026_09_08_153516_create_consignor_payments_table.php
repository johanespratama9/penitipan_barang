<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignor_id')->constrained('consignors')->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('transfer'); // transfer, cash, other
            $table->string('reference_number')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->foreignId('paid_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('payment_number');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignor_payments');
    }
};
