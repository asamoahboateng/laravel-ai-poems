<?php

namespace App\Livewire\Poems;

use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $genre = '';

    public string $subject = '';

    public Collection $genres;

    public Collection $subjects;

    protected $queryString = [
        'search' => ['except' => ''],
        'genre' => ['except' => ''],
        'subject' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->genres = Genre::orderBy('name')->get();
        $this->subjects = Subject::orderBy('name')->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGenre(): void
    {
        $this->resetPage();
    }

    public function updatingSubject(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'genre', 'subject']);
        $this->resetPage();
    }

    public function render()
    {
        $poems = Poem::query()
            ->published()
            ->with(['genre', 'subject'])
            ->when($this->genre, function ($query) {
                $query->whereHas('genre', fn ($q) => $q->where('slug', $this->genre));
            })
            ->when($this->subject, function ($query) {
                $query->whereHas('subject', fn ($q) => $q->where('slug', $this->subject));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%")
                        ->orWhere('author', 'like', "%{$this->search}%");
                });
            })
            ->latest('published_at')
            ->paginate(12);

        return view('livewire.poems.index', compact('poems'));
    }
}
