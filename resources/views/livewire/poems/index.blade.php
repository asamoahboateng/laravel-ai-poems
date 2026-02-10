<div>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-6 text-3xl font-bold text-gray-900">Browse Poems</h1>

        {{-- Filters --}}
        <div class="mb-8 flex flex-wrap items-end gap-4 rounded-lg border border-gray-200 bg-white p-4">
            <div class="w-full sm:w-auto">
                <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" id="search" placeholder="Search poems..."
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:w-64">
            </div>
            <div class="w-full sm:w-auto">
                <label for="genre" class="mb-1 block text-sm font-medium text-gray-700">Genre</label>
                <select wire:model.live="genre" id="genre" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:w-48">
                    <option value="">All Genres</option>
                    @foreach ($genres as $g)
                        <option value="{{ $g->slug }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="subject" class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                <select wire:model.live="subject" id="subject" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:w-48">
                    <option value="">All Subjects</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s->slug }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button wire:click="clearFilters" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</button>
            </div>
        </div>

        {{-- Results --}}
        @if ($poems->isEmpty())
            <p class="text-center text-gray-500">No poems found.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($poems as $poem)
                    <x-poem-card :poem="$poem" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $poems->links() }}
            </div>
        @endif
    </div>
</div>
