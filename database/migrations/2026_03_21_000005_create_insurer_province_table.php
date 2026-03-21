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
        Schema::create('insurer_province', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained('insurers')->cascadeOnDelete();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('pdf_url')->nullable();
            $table->string('pdf_local_path')->nullable();
            $table->json('localities_covered')->nullable();
            $table->json('specialties_available')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['insurer_id', 'province_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurer_province');
    }
};
