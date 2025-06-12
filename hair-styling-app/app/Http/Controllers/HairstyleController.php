<?php

namespace App\Http\Controllers;

use App\Models\Hairstyle;
use Illuminate\Http\Request;

class HairstyleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $hairstyles = Hairstyle::query()
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->get();

        return view('hairstyles.index', compact('hairstyles', 'search'));
    }
}
