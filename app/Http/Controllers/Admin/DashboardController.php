<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Page;
use App\Models\Content;
use App\Models\Menu;
use App\Models\CustomRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'pages' => Page::count(),
            'published_pages' => Page::published()->count(),
            'draft_pages' => Page::draft()->count(),
            'menus' => Menu::count(),
            'active_menus' => Menu::active()->count(),
            'contents' => Content::count(),
            'active_contents' => Content::active()->count(),
            'custom_routes' => CustomRoute::count(),
            'active_routes' => CustomRoute::active()->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentPages = Page::with('creator')->latest()->take(5)->get();
        $recentActivities = DB::table('activity_log')
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('activity_log.*', 'users.name as user_name')
            ->orderBy('activity_log.created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPages', 'recentActivities'));
    }

    public function systemHealth()
    {
        $health = [
            'storage' => [
                'total' => disk_total_space(storage_path()),
                'free' => disk_free_space(storage_path()),
                'used' => disk_total_space(storage_path()) - disk_free_space(storage_path()),
            ],
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
            'database_connections' => DB::connection()->getPdo() ? 'Connected' : 'Disconnected',
            'queue_workers' => 'Active', // This would check your queue workers
        ];

        return response()->json($health);
    }
}