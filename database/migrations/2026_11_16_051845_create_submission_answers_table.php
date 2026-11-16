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
       Schema::create('submission_answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_submission_id')->constrained()->cascadeOnDelete();
    $table->foreignId('question_id')->constrained()->cascadeOnDelete();
    $table->text('answer')->nullable();
    $table->boolean('is_correct')->nullable();
    $table->text('explanation')->nullable();
    $table->unique(['exam_submission_id', 'question_id']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
    }
};
