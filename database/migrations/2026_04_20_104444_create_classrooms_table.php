<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
                $table->foreignId('major_program_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('major_concentration_id')->nullable()->constrained()->nullOnDelete();
                $table->string('nama_kelas');
                $table->timestamps();
            });
        } else {
            // Add missing columns if table exists from a prior run
            Schema::table('classrooms', function (Blueprint $table) {
                if (!Schema::hasColumn('classrooms', 'major_program_id')) {
                    $table->foreignId('major_program_id')->nullable()->constrained()->nullOnDelete()->after('academic_year_id');
                }
                if (!Schema::hasColumn('classrooms', 'major_concentration_id')) {
                    $table->foreignId('major_concentration_id')->nullable()->constrained()->nullOnDelete()->after('major_program_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
