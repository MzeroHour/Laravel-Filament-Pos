<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateCustomer extends Component implements HasActions, HasSchemas
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

                Section::make('Create Customer')
                    ->description('Create a new customer with all the details of the customer.')
                    ->columns(2)
                    ->schema([
                        // ...
                        TextInput::make('name')->label('Customer Name')->required(),
                        TextInput::make('email')->label('Email')->email()->required(),
                        TextInput::make('phone')->label('Phone')->tel()->required(),
                    ])
            ])
            ->statePath('data')
            ->model(Customer::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Customer::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()
            ->title('Customer Created')
            ->body('The customer has been created successfully.')
            ->success()
            ->send();
        redirect()->route('customers.index');
    }

    public function render(): View
    {
        return view('livewire.customers.create-customer');
    }
}
