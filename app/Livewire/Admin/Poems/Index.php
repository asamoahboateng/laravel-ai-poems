<?php

namespace App\Livewire\Admin\Poems;

use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
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
            ->query(Poem::query()->with(['genre', 'subject']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('author')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('genre.name')
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('Draft'),
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label('Add Poem')
                    ->slideOver()
                    ->modalHeading('Create Poem')
                    ->schema($this->formSchema())
                    ->action(function (array $data): void {
                        Poem::create([
                            ...$data,
                            'slug' => Str::slug($data['title']),
                        ]);

                        Notification::make()
                            ->title('Poem created successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->slideOver()
                    ->modalHeading('Edit Poem')
                    ->fillForm(fn (Poem $record): array => $record->toArray())
                    ->schema($this->formSchema())
                    ->action(function (array $data, Poem $record): void {
                        $record->update([
                            ...$data,
                            'slug' => Str::slug($data['title']),
                        ]);

                        Notification::make()
                            ->title('Poem updated successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Poem $record): void {
                        $record->delete();

                        Notification::make()
                            ->title('Poem deleted successfully.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function formSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Textarea::make('content')
                ->required()
                ->rows(10),
            TextInput::make('author')
                ->maxLength(255),
            Select::make('genre_id')
                ->label('Genre')
                ->required()
                ->options(Genre::orderBy('name')->pluck('name', 'id'))
                ->searchable(),
            Select::make('subject_id')
                ->label('Subject')
                ->required()
                ->options(Subject::orderBy('name')->pluck('name', 'id'))
                ->searchable(),
            Toggle::make('is_featured')
                ->label('Featured on homepage')
                ->default(false),
            DateTimePicker::make('published_at')
                ->label('Publish Date')
                ->helperText('Leave blank for draft'),
        ];
    }

    public function render()
    {
        return view('livewire.admin.poems.index');
    }
}
