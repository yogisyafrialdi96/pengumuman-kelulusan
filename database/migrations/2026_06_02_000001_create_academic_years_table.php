<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->dateTime('announcement_datetime')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
