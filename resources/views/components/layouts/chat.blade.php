@props(['title' => 'Chat'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Rhymes & Poems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden font-sans antialiased">
    <div class="flex h-screen flex-col">
        {{-- Compact top navbar --}}
        <nav class="flex h-12 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4">
            <a href="{{ route('home') }}" wire:navigate class="text-sm font-bold text-indigo-600">Rhymes & Poems</a>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" wire:navigate class="text-xs font-medium text-gray-600 hover:text-indigo-600">Home</a>
                <a href="{{ route('poems.index') }}" wire:navigate class="text-xs font-medium text-gray-600 hover:text-indigo-600">Browse</a>
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs font-medium text-gray-600 hover:text-indigo-600">Dashboard</a>
            </div>
        </nav>

        {{-- Main content fills remaining height --}}
        <main class="flex-1 overflow-hidden">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
