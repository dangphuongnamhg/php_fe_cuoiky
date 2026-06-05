<?php

namespace Database\Seeders;

use App\Models\Pitch;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $pitches = Pitch::all();
        foreach ($pitches as $pitch) {
            for ($dow = 0; $dow <= 6; $dow++) {
                for ($h = 6; $h < 24; $h++) {
                    TimeSlot::create([
                        'pitch_id' => $pitch->id,
                        'day_of_week' => $dow,
                        'start_time' => sprintf('%02d:00', $h),
                        'end_time' => sprintf('%02d:30', $h),
                        'is_active' => true,
                    ]);
                    TimeSlot::create([
                        'pitch_id' => $pitch->id,
                        'day_of_week' => $dow,
                        'start_time' => sprintf('%02d:30', $h),
                        'end_time' => $h === 23 ? '00:00' : sprintf('%02d:00', $h + 1),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
