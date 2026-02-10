<div>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('poems.index') }}" wire:navigate class="mb-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
            &larr; Back to Browse
        </a>

        <article class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">{{ $poem->genre->name }}</span>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">{{ $poem->subject->name }}</span>
                @if ($poem->is_featured)
                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Featured</span>
                @endif
            </div>

            <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ $poem->title }}</h1>
            @if ($poem->author)
                <p class="mb-6 text-gray-500">by {{ $poem->author }}</p>
            @endif

            <div class="prose max-w-none whitespace-pre-line text-gray-700">{{ $poem->content }}</div>

            <div class="mt-6 border-t border-gray-100 pt-4 text-sm text-gray-400">
                Published {{ $poem->published_at->format('F j, Y') }}
            </div>
        </article>

        {{-- Related --}}
        @if ($relatedPoems->isNotEmpty())
            <section class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">Related Poems</h2>
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($relatedPoems as $related)
                        <x-poem-card :poem="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
