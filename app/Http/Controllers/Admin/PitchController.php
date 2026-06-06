<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pitch;
use Illuminate\Http\Request;

class PitchController extends Controller
{
    public function index(Request $request)
    {
        $query = Pitch::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            // Standard LIKE query uses database collation which typically handles case and accents (e.g., utf8mb4_unicode_ci)
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('pitch_type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pitches = $query->latest()->paginate(7)->withQueryString();
        $allPitchesForSearch = Pitch::select('name', 'address')->get();

        return view('admin.pitches.index', compact('pitches', 'allPitchesForSearch'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'pitch_type' => 'required|in:football,pickleball',
            'price_per_hour' => 'required|numeric',
            'description' => 'nullable',
            'address' => 'required|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('pitches', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

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
            'address' => 'required|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'status' => 'required',
        ]);

        if ($request->hasFile('image_file')) {
            if ($pitch->image_url && file_exists(public_path($pitch->image_url))) {
                @unlink(public_path($pitch->image_url));
            }
            $path = $request->file('image_file')->store('pitches', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $pitch->update($data);
        return back()->with('success', 'Đã cập nhật sân.');
    }

    public function destroy(Pitch $pitch)
    {
        $pitch->delete();
        return back()->with('success', 'Đã xóa sân.');
    }
}
