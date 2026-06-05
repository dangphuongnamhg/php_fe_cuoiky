<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['user_id', 'pitch_id', 'monthly_booking_id', 'booking_date', 'start_time', 'end_time', 'status', 'booking_type', 'total_price', 'notes'];
    protected $casts = ['booking_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function pitch() { return $this->belongsTo(Pitch::class); }
    public function monthlyBooking() { return $this->belongsTo(MonthlyBooking::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}
