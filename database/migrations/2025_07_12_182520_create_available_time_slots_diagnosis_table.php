<?php
declare(strict_types=1);
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
        Schema::create('available_time_slots_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->references('id')->on('practitioners')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('slot_date');
            $table->time('slot_start_time');
            $table->time('slot_end_time');
            $table->timestamps();
            $table->index(['practitioner_id', 'slot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_time_slots_diagnosis');
    }
};
