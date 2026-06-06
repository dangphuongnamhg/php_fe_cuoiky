<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = \Illuminate\Support\Facades\Http::post(config('services.backend.url') . '/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['data']['token']) && isset($data['data']['user'])) {
                // Save token in session
                session(['api_token' => $data['data']['token']]);
                
                // Sync user locally for Auth::user() compatibility
                $userData = $data['data']['user'];
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => '',
                        'role' => $userData['role'] ?? 'user',
                        'status' => $userData['status'] ?? 'active'
                    ]
                );
                
                Auth::login($user, $request->boolean('remember'));
                return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->intended('/');
            }
        }

        return back()->withErrors(['email' => $response->json('message') ?? 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $response = \Illuminate\Support\Facades\Http::post(config('services.backend.url') . '/register', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['data']['token']) && isset($data['data']['user'])) {
                session(['api_token' => $data['data']['token']]);
                
                $userData = $data['data']['user'];
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => '',
                        'role' => $userData['role'] ?? 'user',
                        'status' => $userData['status'] ?? 'active'
                    ]
                );
                
                Auth::login($user);
                return redirect('/');
            }
        }

        return back()->withErrors(['email' => $response->json('message') ?? 'Đăng ký thất bại'])->withInput();
    }

    public function logout(Request $request)
    {
        // Call backend API to invalidate token
        $token = session('api_token');
        if ($token) {
            \Illuminate\Support\Facades\Http::withToken($token)->post(config('services.backend.url') . '/logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $response = \Illuminate\Support\Facades\Http::post(config('services.backend.url') . '/forgot-password', [
            'email' => $request->email
        ]);

        if ($response->successful()) {
            return back()->with('success', $response->json('message') ?? 'Đường dẫn đặt lại mật khẩu đã được gửi đến email của bạn.');
        }

        return back()->withErrors(['email' => $response->json('message') ?? 'Không thể gửi email. Vui lòng kiểm tra lại.']);
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', ['token' => $request->token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $response = \Illuminate\Support\Facades\Http::post(config('services.backend.url') . '/reset-password', [
            'email' => $request->email,
            'token' => $request->token,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập.');
        }

        return back()->withErrors(['email' => $response->json('message') ?? 'Đã có lỗi xảy ra. Token có thể đã hết hạn.']);
    }
}

