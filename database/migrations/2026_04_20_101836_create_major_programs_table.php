<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('major_programs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('major_programs');
    }
};
