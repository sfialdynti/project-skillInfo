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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('exam_date');
            $table->foreignId('students_id')->constrained('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assessors_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('competency_elements_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status')->nullable();
            $table->longText('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
