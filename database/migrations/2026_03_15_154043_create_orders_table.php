<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('orderable_type');
            $table->unsignedBigInteger('orderable_id');
            $table->string('item_name');
            $table->decimal('original_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);
            $table->string('status')->default(OrderStatus::Pending);
            $table->timestamp('expired_at')->nullable();
            $table->string('invoice_number')->unique()->nullable();
            $table->string('invoice_path')->nullable();
            $table->timestamp('invoice_generated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['orderable_type', 'orderable_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
