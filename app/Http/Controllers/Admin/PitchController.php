<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pitch;
use Illuminate\Http\Request;

class PitchController extends Controller
{
    public function index()
    {
        return view('admin.pitches.index', ['pitches' => Pitch::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'pitch_type' => 'required|in:football,pickleball',
            'price_per_hour' => 'required|numeric',
            'description' => 'nullable',
            'image_url' => 'nullable|url',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        Pitch::create($data);
        return back()->with('success', 'Đã thêm sân mới.');
    }

    public function update(Request $request, Pitch $pitch)
    {
        $data = $request->validate([
            'name' => 'required',
            'pitch_type' => 'required',
            'price_per_hour' => 'required|numeric',
            'description' => 'nullable',
            'image_url' => 'nullable',
            'status' => 'required',
        ]);

        $pitch->update($data);
        return back()->with('success', 'Đã cập nhật sân.');
    }

    public function destroy(Pitch $pitch)
    {
        $pitch->delete();
        return back()->with('success', 'Đã xóa sân.');
    }
}
