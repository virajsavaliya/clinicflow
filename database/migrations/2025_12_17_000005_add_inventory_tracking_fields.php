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
        // Add expiry tracking to medicine_stock
        Schema::table('medicine_stock', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->after('qty');
            $table->date('manufactured_date')->nullable()->after('expiry_date');
            $table->string('batch_number')->nullable()->after('manufactured_date');
        });

        // Add reorder and alert settings to medicines
        Schema::table('medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('medicines', 'auto_reorder')) {
                $table->boolean('auto_reorder')->default(false)->after('reorder_level');
            }
            if (!Schema::hasColumn('medicines', 'preferred_supplier')) {
                $table->string('preferred_supplier')->nullable()->after('auto_reorder');
            }
            if (!Schema::hasColumn('medicines', 'reorder_quantity')) {
                $table->integer('reorder_quantity')->default(100)->after('preferred_supplier');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_stock', function (Blueprint $table) {
            $table->dropColumn(['expiry_date', 'manufactured_date', 'batch_number']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['auto_reorder', 'preferred_supplier', 'reorder_quantity']);
        });
    }
};
