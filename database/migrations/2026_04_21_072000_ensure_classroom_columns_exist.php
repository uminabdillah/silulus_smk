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
        if (Schema::hasTable('classrooms')) {
            Schema::table('classrooms', function (Blueprint $table) {
                if (!Schema::hasColumn('classrooms', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
                }
                if (!Schema::hasColumn('classrooms', 'nama_kelas')) {
                    $table->string('nama_kelas')->nullable()->after('academic_year_id');
                }
                if (!Schema::hasColumn('classrooms', 'major_program_id')) {
                    $table->foreignId('major_program_id')->nullable()->constrained()->nullOnDelete()->after('nama_kelas');
                }
                if (!Schema::hasColumn('classrooms', 'major_concentration_id')) {
                    $table->foreignId('major_concentration_id')->nullable()->constrained()->nullOnDelete()->after('major_program_id');
                }
            });
        }

        if (Schema::hasTable('school_profiles')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('school_profiles', 'logo_path')) {
                    $table->string('logo_path')->nullable()->after('nip_kepala');
                }
                if (!Schema::hasColumn('school_profiles', 'kop_surat')) {
                    $table->string('kop_surat')->nullable()->after('logo_path');
                }
                if (!Schema::hasColumn('school_profiles', 'kabupaten')) {
                    $table->string('kabupaten')->nullable()->after('alamat');
                }
                if (!Schema::hasColumn('school_profiles', 'provinsi')) {
                    $table->string('provinsi')->nullable()->after('kabupaten');
                }
                if (!Schema::hasColumn('school_profiles', 'jabatan_penandatangan')) {
                    $table->string('jabatan_penandatangan')->default('Kepala Sekolah')->after('nip_kepala');
                }
                if (!Schema::hasColumn('school_profiles', 'jenjang')) {
                    $table->string('jenjang')->nullable()->after('jabatan_penandatangan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for safety migration
    }
};
