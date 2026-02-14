<?php

use App\Livewire\Admin;
use App\Livewire\Auth;
use App\Livewire\Chat;
use App\Livewire\Home;
use App\Livewire\Poems;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/poems', Poems\Index::class)->name('poems.index');
Route::get('/poems/{poem}', Poems\Show::class)->name('poems.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', Auth\Login::class)->name('login');
    Route::get('/register', Auth\Register::class)->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');

    Route::get('/poems', Admin\Poems\Index::class)->name('poems.index');
    Route::get('/genres', Admin\Genres\Index::class)->name('genres.index');
    Route::get('/subjects', Admin\Subjects\Index::class)->name('subjects.index');
    Route::get('/models', Admin\Models::class)->name('models');
    Route::get('/ai-settings', Admin\AiSettings::class)->name('ai-settings');
    Route::get('/semantic-search', Admin\SemanticSearch::class)->name('semantic-search');
    Route::get('/chat', Chat::class)->name('chat');
});
