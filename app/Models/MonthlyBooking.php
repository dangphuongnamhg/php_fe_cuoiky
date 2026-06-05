<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyBooking extends Model
{
    protected $fillable = ['user_id', 'pitch_id', 'month_start', 'day_of_week', 'start_time', 'end_time', 'hours_per_match', 'matches_count', 'pitch_total', 'extras_price_per_match', 'extras_notes', 'extras_total', 'total_price', 'notes', 'status', 'renewed_to_id', 'last_occurrence_date', 'renewal_notified_at'];
    protected $casts = ['month_start' => 'date', 'last_occurrence_date' => 'date', 'renewal_notified_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function pitch() { return $this->belongsTo(Pitch::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function locks() { return $this->hasMany(MonthlyBookingLock::class); }
    public function renewedTo() { return $this->belongsTo(MonthlyBooking::class, 'renewed_to_id'); }
}
