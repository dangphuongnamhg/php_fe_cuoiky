<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyBookingLock extends Model
{
    protected $fillable = ['monthly_booking_id', 'user_id', 'pitch_id', 'lock_date', 'day_of_week', 'start_time', 'end_time', 'active_from', 'expires_at', 'status', 'released_by', 'released_at'];
    protected $casts = ['lock_date' => 'date', 'active_from' => 'datetime', 'expires_at' => 'datetime', 'released_at' => 'datetime'];

    public function monthlyBooking() { return $this->belongsTo(MonthlyBooking::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function pitch() { return $this->belongsTo(Pitch::class); }
    public function releasedByUser() { return $this->belongsTo(User::class, 'released_by'); }
}
