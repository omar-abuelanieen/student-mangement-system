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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
        $table->string('gateway')->default('paymob');
        $table->string('reference')->unique();
        $table->string('gateway_transaction_id')->nullable()->unique();
        $table->unsignedInteger('amount_cents');
        $table->string('status')->default('pending');
        $table->json('raw_payload')->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->timestamp('receipt_sent_at')->nullable();
        $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
