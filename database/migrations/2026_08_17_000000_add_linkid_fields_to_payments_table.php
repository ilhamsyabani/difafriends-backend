<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Jadikan midtrans_transaction_id nullable agar bisa cohabitate
            // dengan Link.id payments yang tidak memiliki midtrans_transaction_id
            $table->string('midtrans_transaction_id')->nullable()->change();

            // Field khusus Link.id
            $table->string('linkid_transaction_id')->nullable()->unique();
            $table->json('linkid_response')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('midtrans_transaction_id')->unique()->change();

            $table->dropColumn('linkid_transaction_id');
            $table->dropColumn('linkid_response');
        });
    }
};
