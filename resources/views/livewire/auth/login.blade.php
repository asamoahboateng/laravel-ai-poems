<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Sign In</h1>
        <p class="mt-2 text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500">Create one</a>
        </p>
    </div>

    <form wire:submit="login" class="space-y-6">
        {{ $this->form }}

        @error('data.email')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to home</a>
    </div>
</div>
