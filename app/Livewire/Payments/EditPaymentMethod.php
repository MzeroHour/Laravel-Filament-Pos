<?php

namespace App\Livewire\Payments;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\PaymentMethod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\SelectColumn;

class EditPaymentMethod extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public PaymentMethod $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Edit Payment Method')
                    ->description('Update the details of the payment method.')
                    ->columns(2)
                    ->schema([
                        // ...
                        TextInput::make('name')->label('Name'),
                        // TextInput::make('type')->label('Type'),
                        Select::make('type')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'mobile' => 'Mobile',
                                'banking' => 'Banking',
                                'other' => 'Other',
                            ])->native(false)->searchable(),
                        TextInput::make('description')->label('Description')->columnSpanFull(),

                    ])

            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
        Notification::make()
            ->title('Payment method updated successfully')
            ->body(" The payment method {$this->record->name} has been updated successfully.")
            ->success()
            ->send();
        redirect()->route('payments.index');
    }

    public function render(): View
    {
        return view('livewire.payments.edit-payment-method');
    }
}
