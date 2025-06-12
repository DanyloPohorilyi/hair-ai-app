<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SecretImage;

class AdminImageController extends Controller
{
    public function index()
    {
        $images = SecretImage::paginate(10);
        return view('admin.images.index', compact('images'));
    }

    public function edit($id)
    {
        $image = SecretImage::findOrFail($id);
        return view('admin.images.edit', compact('image'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'haircut' => 'required|string|max:500',
        ]);

        $image = SecretImage::findOrFail($id);
        $image->haircut = $request->haircut;
        $image->save();

        return redirect()->route('admin.images.index')->with('success', 'Дані оновлено!');
    }

    public function destroy($id)
    {
        $image = SecretImage::findOrFail($id);
        $image->delete();

        return redirect()->route('admin.images.index')->with('success', 'Зображення видалено!');
    }
}
