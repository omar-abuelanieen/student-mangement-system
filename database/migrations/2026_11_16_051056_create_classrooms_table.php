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
      Schema::create('classrooms', function (Blueprint $table) {
    $table->id();
    $table->foreignId('level_id')->constrained()->cascadeOnDelete();
    $table->integer('section');
    $table->string('custom_name')->nullable();
    $table->unsignedInteger('capacity')->default(30);
    $table->unique(['level_id', 'section']);
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
