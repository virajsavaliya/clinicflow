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
        Schema::table('medicines', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('medicines', 'generic_name')) {
                $table->string('generic_name')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'type')) {
                $table->string('type')->default('tablet'); // tablet, capsule, syrup, injection, cream, drops, other
            }
            if (!Schema::hasColumn('medicines', 'dosage')) {
                $table->string('dosage')->nullable();
            }
            if (!Schema::hasColumn('medicines', 'quantity')) {
                $table->integer('quantity')->default(0);
            }
            if (!Schema::hasColumn('medicines', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumnIfExists('generic_name');
            $table->dropColumnIfExists('type');
            $table->dropColumnIfExists('dosage');
            $table->dropColumnIfExists('quantity');
            $table->dropColumnIfExists('description');
        });
    }
};
