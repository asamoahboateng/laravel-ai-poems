<div>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-indigo-600 to-purple-700 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold sm:text-5xl">Nursery Rhymes & Poems</h1>
            <p class="mt-4 text-lg text-indigo-100">Discover classic rhymes and beautiful poems for all ages.</p>
            <a href="{{ route('poems.index') }}" wire:navigate class="mt-6 inline-block rounded-lg bg-white px-6 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                Browse All Poems
            </a>
        </div>
    </section>

    {{-- Poem Slider --}}
    @if ($poems->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-900">Featured & Latest</h2>
            <div class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth">
                @foreach ($poems as $poem)
                    <div class="w-80 flex-shrink-0 snap-center rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-3 flex items-center gap-2">
                            @if ($poem->is_featured)
                                <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700">Featured</span>
                            @endif
                            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $poem->genre->name }}</span>
                        </div>
                        <h3 class="mb-1 text-lg font-semibold text-gray-900">{{ $poem->title }}</h3>
                        @if ($poem->author)
                            <p class="mb-3 text-sm text-gray-500">by {{ $poem->author }}</p>
                        @endif
                        <p class="mb-4 line-clamp-4 text-sm text-gray-600 whitespace-pre-line">{{ Str::limit($poem->content, 120) }}</p>
                        <a href="{{ route('poems.show', $poem) }}" wire:navigate class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Read more &rarr;</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
