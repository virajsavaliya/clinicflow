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
        Schema::create('medicine_stock', function (Blueprint $table) {
            $table->foreignId('medicine_id')->primary()->constrained('medicines')->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->timestamps();
            
            $table->index('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_stock');
    }
};
