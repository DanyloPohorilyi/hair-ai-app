<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Hairstyle;
use App\Models\Image;
use App\Models\Recommendation;

class AdminController extends Controller
{
    public function index()
    {
        $customersCount = Customer::count();
        $hairstylesCount = Hairstyle::count();
        $imagesCount = Image::count();
        $recommendationsCount = Recommendation::count();

        return view('admin.dashboard', compact('customersCount', 'hairstylesCount', 'imagesCount', 'recommendationsCount'));
    }
}
