<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
        <p class="mt-2 text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
        </p>
    </div>

    <form wire:submit="register" class="space-y-6">
        {{ $this->form }}

        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create Account
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to home</a>
    </div>
</div>
