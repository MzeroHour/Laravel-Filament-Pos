<?php

namespace App\Livewire\Users;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => User::query())
            ->columns([
                //
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('role')->label('Role')->searchable()->sortable()->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'other' => 'gray',
                        'user' => 'warning',
                        'admin' => 'success',
                        'cashier' => 'danger',
                    }),
                TextColumn::make('created_at')->label('Created At')->toggleable(isToggledHiddenByDefault: true)->date(),
                TextColumn::make('updated_at')->label('Updated At')->toggleable(isToggledHiddenByDefault: true)->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->label('Create New User')
                    ->color('success')
                    ->url(fn(): string => route('users.create'))

            ])
            ->recordActions([
                //
                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(User $record): string => route('users.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(User $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User Deleted')
                            ->body('The user has been deleted successfully.'),
                    )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.users.list-users');
    }
}
