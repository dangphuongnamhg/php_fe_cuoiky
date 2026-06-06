<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pitch extends Model
{
    protected $fillable = ['name', 'pitch_type', 'description', 'address', 'price_per_hour', 'status', 'image_url'];

    public function timeSlots() { return $this->hasMany(TimeSlot::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function monthlyBookings() { return $this->hasMany(MonthlyBooking::class); }

    public function scopeActive($q) { return $q->where('status', 'active'); }
}
