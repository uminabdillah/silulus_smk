<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'classroom_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete()->after('academic_year_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};
