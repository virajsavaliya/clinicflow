<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds performance indexes on frequently queried columns.
     */
    public function up(): void
    {
        // Add composite index for common visit queries
        if (!$this->indexExists('visits', 'visits_doctor_date_status_idx')) {
            Schema::table('visits', function (Blueprint $table) {
                // Composite index for dashboard queries (doctor + date + status)
                $table->index(['doctor_id', 'created_at', 'status'], 'visits_doctor_date_status_idx');
            });
        }

        // Add index for prescription lookups
        if (!$this->indexExists('prescriptions', 'prescriptions_visit_id_index')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->index('visit_id', 'prescriptions_visit_id_index');
            });
        }

        // Add index for prescription items
        if (!$this->indexExists('prescription_items', 'prescription_items_prescription_id_index')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                $table->index('prescription_id', 'prescription_items_prescription_id_index');
            });
        }
        
        if (!$this->indexExists('prescription_items', 'prescription_items_medicine_id_index')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                $table->index('medicine_id', 'prescription_items_medicine_id_index');
            });
        }

        // Add index for bills
        if (!$this->indexExists('bills', 'bills_visit_id_index')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->index('visit_id', 'bills_visit_id_index');
            });
        }
        
        if (!$this->indexExists('bills', 'bills_status_index')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->index('status', 'bills_status_index');
            });
        }

        // Add index for bill items
        if (!$this->indexExists('bill_items', 'bill_items_bill_id_index')) {
            Schema::table('bill_items', function (Blueprint $table) {
                $table->index('bill_id', 'bill_items_bill_id_index');
            });
        }

        // Add index for medicine stock lookups
        if (!$this->indexExists('medicine_stock', 'medicine_stock_medicine_id_index')) {
            Schema::table('medicine_stock', function (Blueprint $table) {
                $table->index('medicine_id', 'medicine_stock_medicine_id_index');
            });
        }

        // Add index for user role queries (common for middleware checks)
        if (!$this->indexExists('users', 'users_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'users_role_index');
            });
        }

        // Add index for notifications (user_id + is_read for unread notification queries)
        if (!$this->indexExists('notifications', 'notifications_user_id_is_read_index')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id', 'is_read'], 'notifications_user_id_is_read_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex('visits_doctor_date_status_idx');
        });

        $this->dropIndexIfExists('prescriptions', 'prescriptions_visit_id_index');
        $this->dropIndexIfExists('prescription_items', 'prescription_items_prescription_id_index');
        $this->dropIndexIfExists('prescription_items', 'prescription_items_medicine_id_index');
        $this->dropIndexIfExists('bills', 'bills_visit_id_index');
        $this->dropIndexIfExists('bills', 'bills_status_index');
        $this->dropIndexIfExists('bill_items', 'bill_items_bill_id_index');
        $this->dropIndexIfExists('medicine_stock', 'medicine_stock_medicine_id_index');
        $this->dropIndexIfExists('users', 'users_role_index');
        $this->dropIndexIfExists('notifications', 'notifications_user_id_is_read_index');
    }

    /**
     * Check if an index exists on a table using raw SQL.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }

    /**
     * Drop index if it exists.
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
