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
       Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
    $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
    $table->foreignId('marked_by')->constrained('teacher_profiles')->cascadeOnDelete();
    $table->date('date');
    $table->string('status');
    $table->timestamp('marked_at',3)->useCurrent();
    $table->unique(['student_profile_id', 'date']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
