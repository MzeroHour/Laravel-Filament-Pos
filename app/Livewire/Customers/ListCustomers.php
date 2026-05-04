<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Dom\Text;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;

class ListCustomers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Customer::query())
            ->columns([
                //
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('phone')->label('Phone')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Created At')->toggleable(isToggledHiddenByDefault: true)->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //

                Action::make('create')
                    ->url(route('customers.create'))
                    ->icon('heroicon-o-plus')
                    ->label('Create New Customer')
                    ->color('success'),
            ])
            ->recordActions([
                //

                //Edit Actions::make('edit')
                Action::make('edit')
                    ->url(fn(Customer $record): string => route('customers.edit', $record))
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn(Customer $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Customer Deleted')
                            ->body('The customer has been deleted successfully.'),
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
        return view('livewire.customers.list-customers');
    }
}
