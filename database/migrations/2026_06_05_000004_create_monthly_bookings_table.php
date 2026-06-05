<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monthly_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pitch_id')->constrained()->cascadeOnDelete();
            $table->date('month_start');
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('hours_per_match', 5, 2)->default(0);
            $table->unsignedInteger('matches_count')->default(0);
            $table->decimal('pitch_total', 12, 2)->default(0);
            $table->decimal('extras_price_per_match', 10, 2)->default(0);
            $table->string('extras_notes')->nullable();
            $table->decimal('extras_total', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'cancelled', 'renewed'])->default('active');
            $table->unsignedBigInteger('renewed_to_id')->nullable();
            $table->date('last_occurrence_date')->nullable();
            $table->timestamp('renewal_notified_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('monthly_bookings'); }
};
