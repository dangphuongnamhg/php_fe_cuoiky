<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MonthlyBooking;
use Illuminate\Http\Request;
use App\Traits\ConsumesBackendApi;

class BookingController extends Controller
{
    use ConsumesBackendApi;

    public function confirm(Request $request)
    {
        $isMonthly = $request->type === 'monthly';

        if ($isMonthly) {
            $response = $this->api()->post('/monthly-bookings/check-availability', [
                'pitch_id' => $request->pitch_id,
                'month' => $request->month,
                'day_of_week' => $request->day,
                'start_time' => $request->start,
                'end_time' => $request->end,
            ]);
        } else {
            $response = $this->api()->post('/bookings/check-availability', [
                'pitch_id' => $request->pitch_id,
                'booking_date' => $request->date,
                'start_time' => $request->start,
                'end_time' => $request->end,
            ]);
        }

        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error('Check Availability Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            return redirect()->route('home')->with('error', $response->json('message', 'Không thể đặt giờ này. Lỗi từ backend.'));
        }

        $data = $response->json('data');

        if ($isMonthly) {
            $dayNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
            $dayOfWeek = $dayNames[$request->day ?? 0];
            return view('bookings.confirm', [
                'pitch' => (object)$data['pitch'],
                'date' => 'Tháng ' . \Carbon\Carbon::parse($request->month)->format('m/Y'),
                'dayOfWeek' => $dayOfWeek,
                'start' => substr($data['start_time'], 0, 5),
                'end' => substr($data['end_time'], 0, 5),
                'basePrice' => $data['total_price'],
                'month' => $request->month,
                'day' => $request->day,
            ]);
        }

        $dow = \Carbon\Carbon::parse($data['booking_date'])->dayOfWeekIso;
        $dayOfWeek = $dow === 7 ? 'Chủ nhật' : 'Thứ ' . ($dow + 1);

        return view('bookings.confirm', [
            'pitch' => (object)$data['pitch'],
            'date' => \Carbon\Carbon::parse($data['booking_date'])->format('d/m/Y'),
            'dayOfWeek' => $dayOfWeek,
            'start' => substr($data['start_time'], 0, 5),
            'end' => substr($data['end_time'], 0, 5),
            'basePrice' => $data['total_price'],
        ]);
    }

    public function store(Request $request)
    {
        $isMonthly = $request->type === 'monthly';
        
        if ($isMonthly) {
            $response = $this->api()->post('/monthly-bookings/create-url', [
                'pitch_id' => $request->pitch_id,
                'month' => $request->month,
                'day_of_week' => $request->day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'extras_price' => $request->extras_price,
                'extras_notes' => $request->extras_notes,
                'notes' => '',
            ]);
        } else {
            $response = $this->api()->post('/payments/create-url', [
                'pitch_id' => $request->pitch_id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'extras_price' => $request->extras_price,
                'extras_notes' => $request->extras_notes,
                'notes' => '',
            ]);
        }

        if ($response->successful()) {
            $data = $response->json('data');
            return redirect()->away($data['payment_url']);
        }

        return back()->with('error', 'Lỗi thanh toán: ' . $response->json('message', 'Không thể tạo thanh toán.'));
    }

    public function history()
    {
        $response = $this->api()->get('/bookings/history');
        
        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error('History fetch failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        $bookingsData = $response->successful() ? $response->json('data.data') ?? [] : [];
        
        $bookings = collect($bookingsData)->map(function ($item) {
            return (object) [
                'id' => $item['id'] ?? 0,
                'code' => 'FB-' . date('Ym') . '-' . str_pad($item['id'] ?? 0, 4, '0', STR_PAD_LEFT),
                'pitch_name' => $item['pitch']['name'] ?? 'Không rõ',
                'date' => \Carbon\Carbon::parse($item['booking_date'])->format('d/m/Y'),
                'time_slot' => substr($item['start_time'] ?? '', 0, 5) . '-' . substr($item['end_time'] ?? '', 0, 5),
                'type' => $item['booking_type'] ?? 'hourly',
                'status' => $item['status'] ?? 'pending',
                'total' => $item['total_price'] ?? 0,
            ];
        });
        
        return view('bookings.history', compact('bookings'));
    }

    public function cancel($id)
    {
        $response = $this->api()->patch("/bookings/{$id}/cancel");
        if ($response->successful()) {
            return back()->with('success', 'Đã hủy đơn đặt sân.');
        }
        return back()->with('error', 'Hủy thất bại: ' . $response->json('message', 'Lỗi không xác định.'));
    }

    public function renew($id)
    {
        // Monthly booking renew... we can just pass a stdClass with id.
        $monthlyBooking = (object)['id' => $id];
        return view('bookings.renew', compact('monthlyBooking'));
    }
}
