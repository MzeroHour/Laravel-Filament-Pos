<?php

namespace App\Livewire\Payments;

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
use App\Models\PaymentMethod;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListPaymentMethods extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => PaymentMethod::query())
            ->columns([
                //
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('type')->label('Type')->searchable()->sortable(),
                TextColumn::make('description')->label('Description')->limit(30)->searchable()->sortable(),
                TextColumn::make('created_at')->label('Created At')->toggleable(isToggledHiddenByDefault: true)->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
                Action::make('create')
                    ->icon('heroicon-o-plus')
                    ->label('Create New Payment Method')
                    ->color('success')
                    ->url(fn(): string => route('payments.create'))
            ])
            ->recordActions([
                //

                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(PaymentMethod $record): string => route('payments.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(PaymentMethod $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Payment Method Deleted')
                            ->body('The payment method has been deleted successfully.'),
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
        return view('livewire.payments.list-payment-methods');
    }
}
