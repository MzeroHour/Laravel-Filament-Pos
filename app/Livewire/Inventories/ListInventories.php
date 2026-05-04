<?php

namespace App\Livewire\Inventories;

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
use App\Models\Inventory;
use Dom\Text;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListInventories extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Inventory::query())
            ->columns([
                //
                TextColumn::make('item.name')->label('Item Name')->searchable()->sortable(),
                TextColumn::make('quantity')->label('Quantity')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Created At')->toggleable(isToggledHiddenByDefault: true)->date(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->label('Create New Inventory')
                    ->color('success')
                    ->url(fn(): string => route('inventories.create'))
            ])
            ->recordActions([
                //

                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(Inventory $record): string => route('inventories.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(Inventory $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Inventory Deleted')
                            ->body('The inventory has been deleted successfully.'),
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
        return view('livewire.inventories.list-inventories');
    }
}
