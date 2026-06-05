<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pitches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('pitch_type', ['football', 'pickleball'])->default('football');
            $table->text('description')->nullable();
            $table->decimal('price_per_hour', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pitches'); }
};
