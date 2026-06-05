<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['pitch_id', 'day_of_week', 'start_time', 'end_time', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function pitch() { return $this->belongsTo(Pitch::class); }
}
