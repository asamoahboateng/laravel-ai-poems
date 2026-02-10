<?php

namespace App\Livewire;

use App\Models\Poem;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public Collection $poems;

    public function mount(): void
    {
        $this->poems = Poem::query()
            ->published()
            ->featuredFirst()
            ->with(['genre', 'subject'])
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.home');
    }
}
