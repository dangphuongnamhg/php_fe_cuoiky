<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pitch;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('admin.pos', ['pitches' => Pitch::active()->get()]);
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Đã tạo đơn đặt sân.');
    }
}
