<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorController as AdminEditorController;
use App\Http\Controllers\Admin\ArchiveNewsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/haber/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/editör/{id}', [EditorController::class, 'show'])->name('editor.show');
Route::get('/editörler', [EditorController::class, 'index'])->name('editors.index');
Route::get('/ara', [NewsController::class, 'search'])->name('search');
Route::get('/api/rates', [\App\Http\Controllers\RatesController::class, 'index'])->name('api.rates');
Route::get('/hava-durumu', [\App\Http\Controllers\WeatherController::class, 'show'])->name('weather.index');
Route::get('/hava-durumu/{city}', [\App\Http\Controllers\WeatherController::class, 'show'])->name('weather.show');
Route::get('/api/weather/{city?}', [\App\Http\Controllers\WeatherController::class, 'widget'])->name('api.weather');
Route::get('/hakkinda', [PageController::class, 'about'])->name('about');
Route::get('/kunye', [PageController::class, 'imprint'])->name('imprint');
Route::get('/künye', [PageController::class, 'imprint']);
Route::get('/iletisim', [PageController::class, 'contact'])->name('contact');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/giris', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/giris', [AdminAuthController::class, 'login']);
    });

    Route::post('/cikis', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware(['auth', 'admin.panel'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/archive-news', [ArchiveNewsController::class, 'index'])->name('archive-news.index');

        Route::resource('news', AdminNewsController::class)->names('news')->except(['show']);
        Route::post('news/{news}/approve', [AdminNewsController::class, 'approve'])->name('news.approve');
        Route::post('news/{news}/reject', [AdminNewsController::class, 'reject'])->name('news.reject');

        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::get('media/list', [MediaController::class, 'list'])->name('media.list');
        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');

        Route::middleware('admin')->group(function () {
            Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');
            Route::resource('categories', AdminCategoryController::class)->names('categories')->except(['show']);
            Route::resource('editors', AdminEditorController::class)->names('editors')->only(['index', 'create', 'store', 'edit', 'update']);
        });
    });
});
