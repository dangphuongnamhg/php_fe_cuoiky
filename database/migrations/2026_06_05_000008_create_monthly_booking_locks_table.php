<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monthly_booking_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pitch_id')->constrained()->cascadeOnDelete();
            $table->date('lock_date');
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->dateTime('active_from');
            $table->dateTime('expires_at');
            $table->enum('status', ['active', 'released', 'expired'])->default('active');
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('monthly_booking_locks'); }
};
