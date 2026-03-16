<?php

use App\Enums\BookingStatus;
use App\Enums\SessionType;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tutor_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tutor_schedule_id')->constrained()->onDelete('restrict');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('type')->default(SessionType::Online);
            $table->string('status')->default(BookingStatus::Pending);
            $table->string('meeting_link')->nullable();
            $table->text('notes')->nullable();
            $table->text('session_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->unique(['tutor_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
