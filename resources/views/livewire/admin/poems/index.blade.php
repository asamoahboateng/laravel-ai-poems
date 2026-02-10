<div>
    <nav class="mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-indigo-600">Dashboard</a>
        <span class="mx-1">/</span>
        <span class="font-medium text-gray-900">Poems</span>
    </nav>

    {{ $this->table }}
</div>
