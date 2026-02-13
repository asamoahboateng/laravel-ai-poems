<?php

namespace App\Livewire\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        }

        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->default('admin@example.com')
                    ->autofocus(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->default('password')
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function login(): void
    {
        $credentials = $this->form->getState();

        if (Auth::attempt($credentials)) {
            session()->regenerate();

            $this->redirect(route('admin.dashboard'), navigate: true);

            return;
        }

        $this->addError('data.email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
