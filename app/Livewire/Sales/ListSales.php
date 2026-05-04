<?php

namespace App\Livewire\Sales;

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
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListSales extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Sale::query())
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(Sale $record): string => route('sales.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(Sale $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sale Deleted')
                            ->body('The sale has been deleted successfully.'),
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
        return view('livewire.sales.list-sales');
    }
}
