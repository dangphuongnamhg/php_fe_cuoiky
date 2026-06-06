<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MonthlyBooking;

class RenewalReminderNotification extends Notification
{
    use Queueable;

    protected $monthlyBooking;

    /**
     * Create a new notification instance.
     */
    public function __construct(MonthlyBooking $monthlyBooking)
    {
        $this->monthlyBooking = $monthlyBooking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'renewal_reminder',
            'monthly_booking_id' => $this->monthlyBooking->id,
            'title' => 'Nhắc nhở gia hạn hợp đồng',
            'message' => 'Hợp đồng thuê sân ' . $this->monthlyBooking->pitch->name . ' của bạn sẽ hết hạn sau 3 ngày nữa. Vui lòng gia hạn để tiếp tục giữ giờ.',
            'action_url' => route('bookings.renew', $this->monthlyBooking->id)
        ];
    }
}
