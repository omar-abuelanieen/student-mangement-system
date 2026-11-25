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
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
        $table->foreignId('guardian_profile_id')->nullable()->constrained()->nullOnDelete();
        $table->string('title');
        $table->unsignedInteger('amount_cents');
        $table->string('currency', 3)->default('EGP');
        $table->string('status')->default('pending');
        $table->date('due_date')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
