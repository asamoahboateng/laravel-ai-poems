<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Semantic Search</h1>
        <p class="mt-1 text-sm text-gray-600">Search poems by meaning using AI embeddings</p>
    </div>

    {{-- Search Form --}}
    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
        <form wire:submit="search" class="flex gap-4">
            <div class="flex-1">
                <input
                    type="text"
                    wire:model="query"
                    placeholder="Describe what you're looking for, e.g. 'poems about nature and seasons'"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none"
                >
                @error('query')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="search" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg wire:loading wire:target="search" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Search
            </button>
        </form>
    </div>

    {{-- Loading State --}}
    <div wire:loading wire:target="search" class="text-center py-12">
        <svg class="mx-auto h-8 w-8 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="mt-3 text-sm text-gray-600">Searching with AI embeddings...</p>
    </div>

    {{-- Results --}}
    <div wire:loading.remove wire:target="search">
        @if ($hasSearched && count($results) === 0)
            <div class="rounded-lg bg-white p-12 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-sm text-gray-500">No poems have been indexed yet. Run <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">php artisan poems:embed</code> to index your poems.</p>
            </div>
        @elseif (count($results) > 0)
            <div class="space-y-4">
                <p class="text-sm text-gray-600">Found {{ count($results) }} results</p>

                @foreach ($results as $result)
                    <div class="rounded-lg bg-white p-6 shadow-sm transition hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('poems.show', $result['poem']['slug']) }}" wire:navigate class="hover:text-indigo-600">
                                            {{ $result['poem']['title'] }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                        {{ $result['similarity'] }}% match
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">by {{ $result['poem']['author'] }}</p>

                                <div class="mt-2 flex gap-2">
                                    @if ($result['poem']['genre'])
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                            {{ $result['poem']['genre'] }}
                                        </span>
                                    @endif
                                    @if ($result['poem']['subject'])
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                            {{ $result['poem']['subject'] }}
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-3 text-sm leading-relaxed text-gray-700">{{ $result['poem']['content'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
