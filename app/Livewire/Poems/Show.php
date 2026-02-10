<?php

namespace App\Livewire\Poems;

use App\Models\Poem;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Poem $poem;

    public Collection $relatedPoems;

    public function mount(Poem $poem): void
    {
        if (! $poem->published_at || $poem->published_at->isFuture()) {
            abort(404);
        }

        $this->poem = $poem->load(['genre', 'subject']);

        $this->relatedPoems = Poem::query()
            ->published()
            ->where('id', '!=', $poem->id)
            ->where(function ($query) use ($poem) {
                $query->where('genre_id', $poem->genre_id)
                    ->orWhere('subject_id', $poem->subject_id);
            })
            ->with(['genre', 'subject'])
            ->limit(4)
            ->inRandomOrder()
            ->get();
    }

    public function render()
    {
        return view('livewire.poems.show');
    }
}
