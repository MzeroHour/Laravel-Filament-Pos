<?php

namespace App\Livewire\Payments;

use App\Models\PaymentMethod;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePaymentMethod extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Create Payment Method')
                    ->description('Create a new payment method with all the details of the payment method.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Payment Name')->required(),
                        Select::make('type')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'mobile' => 'Mobile',
                                'banking' => 'Banking',
                                'other' => 'Other',
                            ])->native(false)->searchable()->required(),
                        Textarea::make('description')->label('Description')->columnSpanFull(),
                    ])
            ])
            ->statePath('data')
            ->model(PaymentMethod::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = PaymentMethod::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()
            ->title('Payment Method Created')
            ->body('The payment method has been created successfully.')
            ->success()
            ->send();
        redirect()->route('payments.index');
    }

    public function render(): View
    {
        return view('livewire.payments.create-payment-method');
    }
}
