<?php
declare(strict_types=1);

use App\Models\Specialty;
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
        Schema::create('practitioners', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->json('specialties')->nullable();
            //$table->json('specialties_ids')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 16)->unique(); // E.164 : max 15 digits including country code plus optional "+" at start
            $table->json('custom_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioners');
    }
};
