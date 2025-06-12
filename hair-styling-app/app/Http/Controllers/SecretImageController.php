<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecretImage;
use App\Models\Hairstyle;
use Illuminate\Support\Facades\Storage;

class SecretImageController extends Controller
{
    public function index()
    {
        $secretImages = SecretImage::orderBy('image_id', 'desc')->paginate(10);
        return view('admin.secret_images.index', compact('secretImages'));
    }

    public function create()
    {
        $hairstyles = Hairstyle::all();
        return view('admin.secret_images.create', compact('hairstyles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'generated_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'haircut' => 'required|string|exists:hairstyle,name'
        ]);

        // **Зберігаємо ОРИГІНАЛЬНИЙ шлях завантаженого файлу**
        $originalFile = $request->file('original_image');
        $originalRealPath = $originalFile->getClientOriginalName(); // Зберігає назву оригінального файлу

        // **Копіюємо оригінальне зображення у `storage`**
        $originalStoredPath = $originalFile->storeAs('secret_images/originals', $originalRealPath, 'public');

        // **Зберігаємо згенероване зображення**
        $generatedFile = $request->file('generated_image');
        $generatedRealPath = 'generated_' . time() . '.' . $generatedFile->getClientOriginalExtension();
        $generatedStoredPath = $generatedFile->storeAs('secret_images/generated', $generatedRealPath, 'public');

        // **Збереження в БД**
        SecretImage::create([
            'original_path' => $originalRealPath, // Зберігаємо оригінальну назву файлу
            'generated_path' => $generatedStoredPath, // Шлях до збереженого файлу
            'haircut' => $request->haircut,
        ]);

        return redirect()->route('admin.secret-images.index')->with('success', 'Зображення додано!');
    }
}
