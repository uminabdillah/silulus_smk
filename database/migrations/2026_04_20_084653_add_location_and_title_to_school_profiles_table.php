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
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('kabupaten')->nullable()->after('alamat');
            $table->string('provinsi')->nullable()->after('kabupaten');
            $table->string('jabatan_penandatangan')->default('Kepala Sekolah')->after('nip_kepala');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn(['kabupaten', 'provinsi', 'jabatan_penandatangan']);
        });
    }
};
