<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Laravel 10+ handles this natively for most databases
        Schema::table('students', function (Blueprint $table) {
            $table->string('status_lulus')->default('tidak lulus')->change();
        });

        // Convert existing boolean values to strings
        // In some databases, boolean is stored as 0/1
        DB::table('students')->where('status_lulus', '1')->update(['status_lulus' => 'lulus']);
        DB::table('students')->where('status_lulus', '0')->update(['status_lulus' => 'tidak lulus']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data to numeric/boolean representation before changing column type
        DB::table('students')->where('status_lulus', 'lulus')->update(['status_lulus' => '1']);
        DB::table('students')->where('status_lulus', 'lulus bersyarat')->update(['status_lulus' => '1']);
        DB::table('students')->where('status_lulus', 'tidak lulus')->update(['status_lulus' => '0']);

        Schema::table('students', function (Blueprint $table) {
            $table->boolean('status_lulus')->default(false)->change();
        });
    }
};
