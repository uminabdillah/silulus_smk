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
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('major_program_id')->nullable()->constrained()->nullOnDelete()->after('konsentrasi_keahlian');
            $table->foreignId('major_concentration_id')->nullable()->constrained()->nullOnDelete()->after('major_program_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['major_program_id']);
            $table->dropForeign(['major_concentration_id']);
            $table->dropColumn(['major_program_id', 'major_concentration_id']);
        });
    }
};
