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
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->date('appointment_date');
                $table->time('appointment_time');
                $table->time('end_time')->nullable();
                $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('scheduled');
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->enum('recurring_frequency', ['daily', 'weekly', 'biweekly', 'monthly'])->nullable();
                $table->date('recurring_end_date')->nullable();
                $table->foreignId('parent_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
                $table->boolean('reminder_sent')->default(false);
                $table->timestamp('reminder_sent_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes for common queries
                $table->index(['doctor_id', 'appointment_date']);
                $table->index(['patient_id', 'appointment_date']);
                $table->index(['appointment_date', 'status']);
                $table->index('status');
            });
        }

        // Doctor availability/schedule table
        if (!Schema::hasTable('doctor_schedules')) {
            Schema::create('doctor_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
                $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('slot_duration')->default(15); // minutes per slot
                $table->integer('max_patients')->nullable(); // max patients per slot (null = unlimited)
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique(['doctor_id', 'day_of_week']);
                $table->index(['doctor_id', 'is_active']);
            });
        }

        // Doctor holidays/blocked dates
        if (!Schema::hasTable('doctor_holidays')) {
            Schema::create('doctor_holidays', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->string('reason')->nullable();
                $table->timestamps();
                
                $table->unique(['doctor_id', 'date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_holidays');
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('appointments');
    }
};
