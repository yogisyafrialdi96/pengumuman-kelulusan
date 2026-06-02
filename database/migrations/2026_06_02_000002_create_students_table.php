<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('nis', 20);
            $table->string('nisn', 10);
            $table->string('nama_siswa');
            $table->string('nama_orang_tua');
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('status', 20)->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'nis']);
            $table->unique(['academic_year_id', 'nisn']);
            $table->index(['academic_year_id', 'status']);
            $table->index(['nisn', 'tanggal_lahir']);
            $table->index(['nis', 'tanggal_lahir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
