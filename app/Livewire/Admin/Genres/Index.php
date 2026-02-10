<?php

namespace App\Livewire\Admin\Genres;

use App\Models\Genre;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Index extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Genre::query()->withCount('poems'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('poems_count')
                    ->label('Poems')
                    ->sortable(),
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label('Add Genre')
                    ->slideOver()
                    ->modalHeading('Create Genre')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Genre::class, 'name'),
                        Textarea::make('description')
                            ->rows(4),
                    ])
                    ->action(function (array $data): void {
                        Genre::create([
                            ...$data,
                            'slug' => Str::slug($data['name']),
                        ]);

                        Notification::make()
                            ->title('Genre created successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->slideOver()
                    ->modalHeading('Edit Genre')
                    ->fillForm(fn (Genre $record): array => $record->toArray())
                    ->schema(fn (Genre $record): array => [
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Genre::class, 'name', ignorable: $record),
                        Textarea::make('description')
                            ->rows(4),
                    ])
                    ->action(function (array $data, Genre $record): void {
                        $record->update([
                            ...$data,
                            'slug' => Str::slug($data['name']),
                        ]);

                        Notification::make()
                            ->title('Genre updated successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Genre $record): void {
                        $record->delete();

                        Notification::make()
                            ->title('Genre deleted successfully.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.admin.genres.index');
    }
}
