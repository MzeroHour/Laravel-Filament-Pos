<?php

namespace App\Livewire\Items;

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
use App\Models\Item;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;

class ListItems extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Item::query())
            ->columns([

                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                // TextColumn::make('quantity')->label('Quantity')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                TextColumn::make('price')->label('Price')->money('')->sortable(),
                // TextColumn::make('status')->label('Status')->badge(),
                // TextColumn::make('description')->label('Description'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'inactive' => 'gray',
                        'reviewing' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger',
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->label('Create New Item')
                    ->color('success')
                    ->url(fn(): string => route('items.create'))
            ])
            ->recordActions([
                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(Item $record): string => route('items.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                //Delete Action
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(Item $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Item Deleted')
                            ->body('The item has been deleted successfully.'),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.items.list-items');
    }
}
