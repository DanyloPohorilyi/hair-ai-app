<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminHairstyleController as AdminAdminHairstyleController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\AdminHairstyleController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HairStylingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
})->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/hairstyles', [App\Http\Controllers\HairstyleController::class, 'index'])->name('hairstyles.index');

// Завантаження фото
Route::get('/upload', [HairStylingController::class, 'showUploadForm'])->name('upload.form');

// Обробка фото (AJAX)
Route::post('/process', [HairStylingController::class, 'processImage'])->name('process.image');

// Перегляд результату
Route::get('/result/show/{id}', [HairStylingController::class, 'showResult'])->name('result.show');

// Перевірка статусу (AJAX)
Route::get('/result-status/{id}', [HairStylingController::class, 'checkResultStatus'])->name('result.status');
Route::get('/result/status/{id}', [HairStylingController::class, 'checkResultStatus']);


// Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
// Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');

Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');

Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');


//Admin
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('customers', AdminCustomerController::class)->names([
        'index' => 'admin.customers.index',
        'create' => 'admin.customers.create',
        'store' => 'admin.customers.store',
        'edit' => 'admin.customers.edit',
        'update' => 'admin.customers.update',
        'destroy' => 'admin.customers.destroy',
    ]);
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/images', [AdminImageController::class, 'index'])->name('admin.images.index');
    Route::get('/images/{id}/edit', [AdminImageController::class, 'edit'])->name('admin.images.edit');
    Route::put('/images/{id}', [AdminImageController::class, 'update'])->name('admin.images.update');
    Route::delete('/images/{id}', [AdminImageController::class, 'destroy'])->name('admin.images.destroy');
    Route::get('/hairstyles', [AdminAdminHairstyleController::class, 'index'])->name('admin.hairstyles.index');
    Route::get('/hairstyles/create', [AdminAdminHairstyleController::class, 'create'])->name('admin.hairstyles.create');
    Route::post('/hairstyles', [AdminAdminHairstyleController::class, 'store'])->name('admin.hairstyles.store');
    Route::get('/hairstyles/{id}/edit', [AdminAdminHairstyleController::class, 'edit'])->name('admin.hairstyles.edit');
    Route::put('/hairstyles/{id}', [AdminAdminHairstyleController::class, 'update'])->name('admin.hairstyles.update');
    Route::delete('/hairstyles/{id}', [AdminAdminHairstyleController::class, 'destroy'])->name('admin.hairstyles.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [UserProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/profile/avatar', [UserProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});

use App\Http\Controllers\SecretImageController;

Route::prefix('admin')->group(function () {
    Route::get('/secret-images', [SecretImageController::class, 'index'])->name('admin.secret-images.index');
    Route::get('/secret-images/create', [SecretImageController::class, 'create'])->name('admin.secret-images.create');
    Route::post('/secret-images/store', [SecretImageController::class, 'store'])->name('admin.secret-images.store');
});
