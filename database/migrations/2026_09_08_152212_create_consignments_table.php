<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignor_id')->constrained('consignors')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->date('received_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, received, approved, rejected, completed, expired
            $table->text('notes')->nullable();
            $table->integer('total_items')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignments');
    }
};
