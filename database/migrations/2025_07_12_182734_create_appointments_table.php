<?php

use App\Models\Appointment;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->references('id')->on('practitioners')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('appointment_date');
            $table->time('appointment_start_time');
            $table->time('appointment_end_time');
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone');
            $table->enum('kind_of_appointment', Appointment::VALID_KINDS);
            $table->enum('status', Appointment::VALID_STATUSES)->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
