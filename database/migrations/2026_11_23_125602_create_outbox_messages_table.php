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
    Schema::create('outbox_messages', function (Blueprint $table) {
        $table->id();
        $table->string('type');
        $table->json('payload');
        $table->string('status')->default('pending');
        $table->unsignedTinyInteger('attempts')->default(0);
        $table->text('last_error')->nullable();
        $table->timestamp('available_at')->useCurrent();
        $table->timestamp('processed_at')->nullable();
        $table->timestamps();

        $table->index(['status', 'available_at']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
