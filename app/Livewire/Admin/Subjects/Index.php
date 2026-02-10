<?php

namespace App\Livewire\Admin\Subjects;

use App\Models\Subject;
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
            ->query(Subject::query()->withCount('poems'))
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
                    ->label('Add Subject')
                    ->slideOver()
                    ->modalHeading('Create Subject')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Subject::class, 'name'),
                        Textarea::make('description')
                            ->rows(4),
                    ])
                    ->action(function (array $data): void {
                        Subject::create([
                            ...$data,
                            'slug' => Str::slug($data['name']),
                        ]);

                        Notification::make()
                            ->title('Subject created successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->slideOver()
                    ->modalHeading('Edit Subject')
                    ->fillForm(fn (Subject $record): array => $record->toArray())
                    ->schema(fn (Subject $record): array => [
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Subject::class, 'name', ignorable: $record),
                        Textarea::make('description')
                            ->rows(4),
                    ])
                    ->action(function (array $data, Subject $record): void {
                        $record->update([
                            ...$data,
                            'slug' => Str::slug($data['name']),
                        ]);

                        Notification::make()
                            ->title('Subject updated successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Subject $record): void {
                        $record->delete();

                        Notification::make()
                            ->title('Subject deleted successfully.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.admin.subjects.index');
    }
}
