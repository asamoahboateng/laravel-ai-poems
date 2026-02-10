@props(['title' => 'Nursery Rhymes & Poems'])

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" wire:navigate class="text-xl font-bold text-indigo-600">
                        Rhymes & Poems
                    </a>
                    <div class="hidden sm:flex sm:gap-6">
                        <a href="{{ route('home') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-indigo-600 {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('poems.index') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-indigo-600 {{ request()->routeIs('poems.*') ? 'text-indigo-600' : '' }}">
                            Browse
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" wire:navigate class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                            Register
                        </a>
                        <a href="{{ route('login') }}" wire:navigate class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-gray-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center text-sm text-gray-500 sm:px-6 lg:px-8">
            &copy; {{ date('Y') }} Rhymes & Poems. All rights reserved.
        </div>
    </footer>

    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>
