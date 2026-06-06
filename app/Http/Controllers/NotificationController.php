<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ConsumesBackendApi;

class NotificationController extends Controller
{
    use ConsumesBackendApi;

    public function index()
    {
        $response = $this->api()->get('/notifications');
        $notificationsData = $response->successful() ? $response->json('data.data') ?? [] : [];
        $notifications = json_decode(json_encode($notificationsData));
        return view('notifications.index', compact('notifications'));
    }

    public function readAll()
    {
        $this->api()->patch('/notifications/read-all');
        return back();
    }

    public function destroy($id)
    {
        $this->api()->delete("/notifications/{$id}");
        return back();
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }
}
