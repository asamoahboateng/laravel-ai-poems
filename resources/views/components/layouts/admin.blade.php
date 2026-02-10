@props(['title' => 'Admin Dashboard'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Admin</title>
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Mobile overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/50 lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-indigo-900 transition-transform duration-200 lg:static lg:translate-x-0">
            <div class="flex h-full flex-col">
                <div class="flex h-16 items-center justify-between px-6">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-lg font-bold text-white">
                        Admin Panel
                    </a>
                    <button class="text-indigo-300 hover:text-white lg:hidden" onclick="toggleSidebar()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 space-y-1 px-3 py-4">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.poems.index') }}" wire:navigate
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.poems.*') ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Poems
                    </a>
                    <a href="{{ route('admin.genres.index') }}" wire:navigate
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.genres.*') ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Genres
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" wire:navigate
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Subjects
                    </a>
                </nav>
                <div class="border-t border-indigo-800 p-4">
                    <div class="mb-2 text-sm text-indigo-300">{{ auth()->user()->name }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-indigo-300 hover:bg-indigo-800 hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex flex-1 flex-col">
            {{-- Mobile header --}}
            <header class="flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 lg:hidden">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="text-lg font-semibold text-gray-900">{{ $title }}</span>
            </header>

            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @filamentScripts
    @vite('resources/js/app.js')

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>
