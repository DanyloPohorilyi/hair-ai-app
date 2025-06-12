<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\ProcessedImage;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $processedImages = ProcessedImage::whereHas('image', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })->get();

        return view('profile.index', compact('user', 'processedImages'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!$user instanceof \App\Models\Customer) {
            return back()->withErrors(['error' => 'Користувач не знайдений.']);
        }

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Дані оновлено!');
    }


    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Переконаємось, що користувач справді авторизований
        if (!$user instanceof \App\Models\Customer) {
            return back()->withErrors(['error' => 'Користувач не знайдений.']);
        }

        // Збереження нового аватара
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        // Видаляємо старий аватар, якщо він не "default.png"
        if ($user->photo && $user->photo !== 'avatars/default.png') {
            Storage::disk('public')->delete($user->photo);
        }

        // Оновлюємо фото в БД
        $user->photo = $avatarPath;
        $user->save();

        return back()->with('success', 'Аватар оновлено!');
    }
}
