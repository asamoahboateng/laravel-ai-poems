@props(['title' => 'Authentication'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Rhymes & Poems</title>
    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Left column: Full image/gradient --}}
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-purple-700 items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1" fill="white"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#dots)"/>
                </svg>
            </div>
            <div class="relative text-center text-white">
                <svg class="mx-auto mb-8 h-20 w-20 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h1 class="text-4xl font-bold mb-4">Nursery Rhymes<br>& Poems</h1>
                <p class="text-xl text-indigo-200 max-w-sm mx-auto">Discover classic rhymes and beautiful poems for all ages</p>
            </div>
        </div>

        {{-- Right column: Form --}}
        <div class="flex w-full lg:w-1/2 items-center justify-center p-8">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>

    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>
