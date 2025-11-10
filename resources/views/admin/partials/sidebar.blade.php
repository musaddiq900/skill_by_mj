<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 transform -translate-x-full lg:translate-x-0 transition duration-200 ease-in-out">
    <div class="flex items-center justify-center h-16 bg-gray-900">
        <span class="text-white text-xl font-semibold">{{ config('app.name') }}</span>
    </div>
    
    <nav class="mt-8">
        <div class="px-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Users
            </a>

            <!-- Pages -->
            <a href="{{ route('admin.pages.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Pages
            </a>

            <!-- Menus -->
            <a href="{{ route('admin.menus.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.menus.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Menus
            </a>

            <!-- Routes -->
            <a href="{{ route('admin.routes.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.routes.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Routes
            </a>

            <!-- Content -->
            <a href="{{ route('admin.contents.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.contents.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Content
            </a>

            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </a>
        </div>
    </nav>
</div>