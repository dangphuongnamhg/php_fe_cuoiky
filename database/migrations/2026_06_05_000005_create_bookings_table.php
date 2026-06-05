<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pitch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monthly_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('booking_type', ['hourly', 'monthly'])->default('hourly');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['pitch_id', 'booking_date', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
