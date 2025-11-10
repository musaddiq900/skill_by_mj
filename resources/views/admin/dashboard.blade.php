@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Users Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Pages Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Total Pages</h3>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pages'] }}</p>
                <p class="text-sm text-gray-500">
                    {{ $stats['published_pages'] }} Published, {{ $stats['draft_pages'] }} Draft
                </p>
            </div>
        </div>
    </div>

    <!-- Menus Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-500 text-white mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Menus</h3>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['menus'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['active_menus'] }} Active</p>
            </div>
        </div>
    </div>

    <!-- Content Blocks Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-500 text-white mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Content Blocks</h3>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['contents'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['active_contents'] }} Active</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentUsers as $user)
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $user->created_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Pages -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Pages</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentPages as $page)
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">{{ $page->title }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 
                           ($page->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($page->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">By {{ $page->creator->name }} • {{ $page->created_at->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @foreach($recentActivities as $activity)
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">{{ $activity->user_name }}</p>
                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ $activity->description }}</p>
            @if($activity->properties)
            <div class="mt-2 text-xs text-gray-500">
                Changes: 
                @foreach(json_decode($activity->properties, true) as $key => $value)
                <span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1">{{ $key }}</span>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection