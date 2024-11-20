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
        Schema::create('competency_standards', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 32);
            $table->string('unit_title');
            $table->longText('unit_description');
            $table->foreignId('majors_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assessors_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_standards');
    }
};
