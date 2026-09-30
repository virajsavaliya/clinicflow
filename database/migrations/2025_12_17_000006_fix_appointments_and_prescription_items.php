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
        // Fix appointments table - add missing columns
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'appointment_time')) {
                $table->time('appointment_time')->nullable()->after('appointment_date');
            }
            if (!Schema::hasColumn('appointments', 'end_time')) {
                $table->time('end_time')->nullable()->after('appointment_time');
            }
            if (!Schema::hasColumn('appointments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('doctor_id');
            }
            if (!Schema::hasColumn('appointments', 'reason')) {
                $table->text('reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('notes');
            }
            if (!Schema::hasColumn('appointments', 'recurring_frequency')) {
                $table->string('recurring_frequency')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('appointments', 'recurring_end_date')) {
                $table->date('recurring_end_date')->nullable()->after('recurring_frequency');
            }
            if (!Schema::hasColumn('appointments', 'parent_appointment_id')) {
                $table->unsignedBigInteger('parent_appointment_id')->nullable()->after('recurring_end_date');
            }
            if (!Schema::hasColumn('appointments', 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('parent_appointment_id');
            }
            if (!Schema::hasColumn('appointments', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('reminder_sent');
            }
            if (!Schema::hasColumn('appointments', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        // Add indexes if they don't exist
        try {
            Schema::table('appointments', function (Blueprint $table) {
                $table->index(['doctor_id', 'appointment_date'], 'apt_doctor_date_idx');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }
        
        // Fix prescription_items - add quantity alias column or rename
        // The column is 'qty' but code expects 'quantity', let's add an alias approach
        // We'll fix the code instead to use 'qty'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $columns = ['appointment_time', 'end_time', 'created_by', 'reason', 
                        'is_recurring', 'recurring_frequency', 'recurring_end_date',
                        'parent_appointment_id', 'reminder_sent', 'reminder_sent_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('appointments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
