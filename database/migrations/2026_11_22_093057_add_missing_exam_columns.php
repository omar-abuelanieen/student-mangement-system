<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('questions', 'options')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->json('options')->nullable()->after('type');
            });
        }

        if (! Schema::hasColumn('exam_submissions', 'status')) {
            Schema::table('exam_submissions', function (Blueprint $table) {
                $table->string('status')->default('submitted')->after('student_profile_id');
            });
        }

        if (! Schema::hasColumn('exam_submissions', 'graded_by')) {
            Schema::table('exam_submissions', function (Blueprint $table) {
                $table->foreignId('graded_by')->nullable()->after('graded_at')
                    ->constrained('teacher_profiles')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('exam_submissions', 'graded_by')) {
            Schema::table('exam_submissions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('graded_by');
            });
        }
    }
};
