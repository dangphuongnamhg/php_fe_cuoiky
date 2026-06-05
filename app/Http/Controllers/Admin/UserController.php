<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('bookings')->get();
        return view('admin.users.index', compact('users'));
    }

    public function toggleLock(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'locked' : 'active']);
        return back()->with('success', $user->status === 'locked' ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }
}
