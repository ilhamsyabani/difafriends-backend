<?php

use App\Enums\CourseStatus;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->boolean('has_certificate')->default(false);
            $table->text('prerequisites')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default(CourseStatus::Draft);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
