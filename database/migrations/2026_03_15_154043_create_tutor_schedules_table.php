<?php

use App\Enums\DayOfWeek;
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
        Schema::create('tutor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users')->onDelete('restrict');
            $table->tinyInteger('day_of_week')->default(DayOfWeek::Sunday);
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('session_duration')->default(60);
            $table->tinyInteger('break_time')->default(15);
            $table->decimal('price', 15, 2);
            $table->integer('max_participants')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tutor_id', 'day_of_week', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_schedules');
    }
};
