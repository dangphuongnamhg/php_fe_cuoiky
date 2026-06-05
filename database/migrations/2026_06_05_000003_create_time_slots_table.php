<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pitch_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=CN, 1=T2,...6=T7
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['pitch_id', 'day_of_week', 'start_time', 'end_time']);
        });
    }
    public function down(): void { Schema::dropIfExists('time_slots'); }
};
