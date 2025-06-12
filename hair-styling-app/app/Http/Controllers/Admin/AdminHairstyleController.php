<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hairstyle;
use Illuminate\Support\Facades\Storage;

class AdminHairstyleController extends Controller
{
    public function index()
    {
        $hairstyles = Hairstyle::paginate(10);
        return view('admin.hairstyles.index', compact('hairstyles'));
    }

    public function create()
    {
        return view('admin.hairstyles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'recommended_for' => 'required|string|max:50',
        ]);

        $imagePath = $request->file('image')->store('hairstyles', 'public');

        Hairstyle::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_path' => $imagePath,
            'recommended_for' => $request->recommended_for,
        ]);

        return redirect()->route('admin.hairstyles.index')->with('success', 'Зачіску додано!');
    }

    public function edit($id)
    {
        $hairstyle = Hairstyle::findOrFail($id);
        return view('admin.hairstyles.edit', compact('hairstyle'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'recommended_for' => 'required|string|max:50',
        ]);

        $hairstyle = Hairstyle::findOrFail($id);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($hairstyle->image_path);
            $imagePath = $request->file('image')->store('hairstyles', 'public');
            $hairstyle->image_path = $imagePath;
        }

        $hairstyle->name = $request->name;
        $hairstyle->description = $request->description;
        $hairstyle->recommended_for = $request->recommended_for;
        $hairstyle->save();

        return redirect()->route('admin.hairstyles.index')->with('success', 'Зачіску оновлено!');
    }

    public function destroy($id)
    {
        $hairstyle = Hairstyle::findOrFail($id);
        Storage::disk('public')->delete($hairstyle->image_path);
        $hairstyle->delete();

        return redirect()->route('admin.hairstyles.index')->with('success', 'Зачіску видалено!');
    }
}
