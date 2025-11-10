<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Super Admin|Admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/system-health', [DashboardController::class, 'systemHealth'])->name('system.health');

    // Users Management
    Route::resource('users', UserController::class);
    Route::get('users/{user}/activities', [UserController::class, 'activities'])->name('users.activities');

    // Pages Management
    Route::resource('pages', PageController::class);
    Route::post('pages/bulk-action', [PageController::class, 'bulkAction'])->name('pages.bulk-action');

    // Menus Management
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');

    // Custom Routes Management
    Route::resource('routes', RouteController::class);
    Route::patch('routes/{route}/toggle', [RouteController::class, 'toggle'])->name('routes.toggle');

    // Content Management
    Route::resource('contents', ContentController::class);

    // Settings Management
    Route::resource('settings', SettingController::class);
    Route::post('settings/bulk-update', [SettingController::class, 'bulkUpdate'])->name('settings.bulk-update');

    // File Manager
    Route::group(['prefix' => 'filemanager'], function () {
        Route::get('/', [\UniSharp\LaravelFilemanager\Controllers\LfmController::class, 'show'])->name('filemanager.index');
        Route::get('/picker', [\UniSharp\LaravelFilemanager\Controllers\LfmController::class, 'show'])->name('filemanager.picker');
    });
});