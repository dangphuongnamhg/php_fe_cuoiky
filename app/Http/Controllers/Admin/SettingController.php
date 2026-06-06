<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        // Handle checkbox toggles which might not be sent if unchecked
        $checkboxes = ['vnpay_enable'];
        foreach ($checkboxes as $cb) {
            $data[$cb] = $request->has($cb) ? 'on' : 'off';
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Đã lưu cấu hình hệ thống thành công.');
    }
}
