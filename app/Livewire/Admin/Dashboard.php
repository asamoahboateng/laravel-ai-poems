<?php

namespace App\Livewire\Admin;

use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    public int $poemCount;

    public int $genreCount;

    public int $subjectCount;

    public Collection $recentPoems;

    public function mount(): void
    {
        $this->poemCount = Poem::count();
        $this->genreCount = Genre::count();
        $this->subjectCount = Subject::count();
        $this->recentPoems = Poem::with('genre')->latest()->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
