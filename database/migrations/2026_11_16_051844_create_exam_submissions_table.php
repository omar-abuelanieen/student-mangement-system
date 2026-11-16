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
       Schema::create('exam_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
    $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
    $table->decimal('score', 6, 2)->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->timestamp('graded_at')->nullable();
    $table->unique(['exam_id', 'student_profile_id']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_submissions');
    }
};
