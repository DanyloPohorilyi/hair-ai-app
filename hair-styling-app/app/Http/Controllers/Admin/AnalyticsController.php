<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Hairstyle;
use App\Models\Image;
use App\Models\ProcessedImage;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Загальні метрики
        $totalUsers = Customer::count();
        $totalAdmins = Customer::where('is_admin', true)->count();
        $totalHairstyles = Hairstyle::count();
        $totalImages = Image::count();
        $totalProcessedImages = ProcessedImage::count();
        $totalRecommendations = Recommendation::count();

        // ТОП-5 найпопулярніших зачісок
        $popularHairstyles = Recommendation::selectRaw('hairstyle_id, COUNT(*) as count')
            ->groupBy('hairstyle_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('hairstyle')
            ->get();

        // Графік реєстрацій за останні 6 місяців
        $userRegistrations = Customer::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return view('admin.analytics.index', compact(
            'totalUsers',
            'totalAdmins',
            'totalHairstyles',
            'totalImages',
            'totalProcessedImages',
            'totalRecommendations',
            'popularHairstyles',
            'userRegistrations'
        ));
    }
}
