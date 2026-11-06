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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('table_number')->nullable()->after('customer_name');
            $table->text('order_notes')->nullable()->after('table_number');
            $table->string('kitchen_status')->default('pending')->after('status'); // pending, cooking, ready, completed, cancelled
            $table->timestamp('kitchen_printed_at')->nullable()->after('sold_at');
            $table->timestamp('cooking_started_at')->nullable()->after('kitchen_printed_at');
            $table->timestamp('cooking_finished_at')->nullable()->after('cooking_started_at');
            $table->timestamp('kitchen_completed_at')->nullable()->after('cooking_finished_at');

            $table->index('kitchen_status');
            $table->index('table_number');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('product_name');
            $table->string('item_status')->default('pending')->after('notes'); // pending, cooking, ready, completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['notes', 'item_status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['kitchen_status']);
            $table->dropIndex(['table_number']);
            $table->dropColumn([
                'table_number',
                'order_notes',
                'kitchen_status',
                'kitchen_printed_at',
                'cooking_started_at',
                'cooking_finished_at',
                'kitchen_completed_at',
            ]);
        });
    }
};
