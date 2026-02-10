<div>
    <nav class="mb-4 text-sm text-gray-500">
        <span class="font-medium text-gray-900">Dashboard</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Dashboard</h1>

    {{-- Stats --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="text-sm font-medium text-gray-500">Total Poems</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $poemCount }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="text-sm font-medium text-gray-500">Genres</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $genreCount }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="text-sm font-medium text-gray-500">Subjects</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $subjectCount }}</div>
        </div>
    </div>

    {{-- Recent Poems --}}
    <div class="rounded-lg border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Poems</h2>
            <a href="{{ route('admin.poems.index') }}" wire:navigate class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">Manage Poems</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Title</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Genre</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($recentPoems as $poem)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $poem->title }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $poem->genre->name }}</td>
                            <td class="px-6 py-4">
                                @if ($poem->published_at)
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Published</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $poem->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
