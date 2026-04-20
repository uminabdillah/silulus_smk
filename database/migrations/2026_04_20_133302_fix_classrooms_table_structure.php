<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (!Schema::hasColumn('classrooms', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
            }
            if (!Schema::hasColumn('classrooms', 'nama_kelas')) {
                $table->string('nama_kelas')->after('academic_year_id')->default('—');
            }
            if (!Schema::hasColumn('classrooms', 'major_program_id')) {
                $table->foreignId('major_program_id')->nullable()->constrained()->nullOnDelete()->after('nama_kelas');
            }
            if (!Schema::hasColumn('classrooms', 'major_concentration_id')) {
                $table->foreignId('major_concentration_id')->nullable()->constrained()->nullOnDelete()->after('major_program_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            // Check existence before dropping to avoid errors during rollback
            if (Schema::hasColumn('classrooms', 'major_concentration_id')) {
                $table->dropForeign(['major_concentration_id']);
                $table->dropColumn('major_concentration_id');
            }
            if (Schema::hasColumn('classrooms', 'major_program_id')) {
                $table->dropForeign(['major_program_id']);
                $table->dropColumn('major_program_id');
            }
            if (Schema::hasColumn('classrooms', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
            if (Schema::hasColumn('classrooms', 'nama_kelas')) {
                $table->dropColumn('nama_kelas');
            }
        });
    }
};
