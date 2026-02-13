<div>
    <nav class="mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-indigo-600">Dashboard</a>
        <span class="mx-1">/</span>
        <span class="font-medium text-gray-900">AI Settings</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">AI Settings</h1>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
