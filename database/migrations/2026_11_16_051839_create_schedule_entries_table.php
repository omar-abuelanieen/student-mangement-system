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
       Schema::create('schedule_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
    $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
    $table->foreignId('teacher_profile_id')->constrained()->cascadeOnDelete();
    $table->string('day');           
    $table->time('start_time');
    $table->time('end_time');
    $table->index(['classroom_id', 'day']);
    $table->index(['teacher_profile_id', 'day']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_entries');
    }
};
