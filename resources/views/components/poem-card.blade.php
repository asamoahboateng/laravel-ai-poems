@props(['poem'])

<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
    <div class="mb-2 flex items-center gap-2">
        <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
            {{ $poem->genre->name }}
        </span>
        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
            {{ $poem->subject->name }}
        </span>
    </div>
    <h3 class="mb-1 text-lg font-semibold text-gray-900">
        <a href="{{ route('poems.show', $poem) }}" class="hover:text-indigo-600">
            {{ $poem->title }}
        </a>
    </h3>
    @if ($poem->author)
        <p class="mb-3 text-sm text-gray-500">by {{ $poem->author }}</p>
    @endif
    <p class="mb-4 line-clamp-3 text-sm text-gray-600">{{ Str::limit($poem->content, 150) }}</p>
    <a href="{{ route('poems.show', $poem) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
        Read more &rarr;
    </a>
</div>
