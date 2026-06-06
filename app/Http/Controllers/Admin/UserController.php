<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $allUsersForSearch = User::select('id', 'name', 'email')->get();
        return view('admin.users.index', compact('users', 'allUsersForSearch'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,locked',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('success', 'Đã thêm người dùng mới.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,locked',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Đã cập nhật thông tin người dùng.');
    }

    public function destroy(User $user)
    {
        if ($user->bookings()->count() > 0) {
            return back()->with('error', 'Không thể xóa người dùng đã có lịch đặt sân.');
        }

        $user->delete();
        return back()->with('success', 'Đã xóa người dùng.');
    }

    public function toggleLock(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'locked' : 'active']);
        return back()->with('success', $user->status === 'locked' ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }
}
