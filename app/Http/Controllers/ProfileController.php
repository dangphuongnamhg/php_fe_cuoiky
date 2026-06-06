<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use \App\Traits\ConsumesBackendApi;

    public function edit()
    {
        // View can use Auth::user() which we synced during login
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'name' => 'required|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Send to backend via API
        $response = $this->api()->put('/user/profile', $data);

        if ($response->successful()) {
            // Update local user session/DB to stay in sync
            $user->update([
                'name' => $data['name'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);
            
            return back()->with('success', 'Cập nhật thành công.');
        }

        return back()->with('error', 'Lỗi cập nhật: ' . $response->json('message', 'Không xác định'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        // Send to backend via API
        $response = $this->api()->put('/user/profile', [
            'name' => $user->name,
            'password' => $data['password'],
        ]);

        if ($response->successful()) {
            $user->update(['password' => Hash::make($data['password'])]);
            return back()->with('success', 'Đổi mật khẩu thành công.');
        }

        return back()->with('error', 'Lỗi cập nhật: ' . $response->json('message', 'Không xác định'));
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();
        
        // Optionally send a delete request to the backend if the backend supports it.
        // For now, just logout and delete local session
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Tài khoản của bạn đã được xóa.');
    }
}
