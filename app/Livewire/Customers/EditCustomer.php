<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditCustomer extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Customer $record;

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
                Section::make('Edit Item')
                    ->description('Update the details of the item.')
                    ->columns(2)
                    ->schema([
                        // ...
                        TextInput::make('name')->label('Name'),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('phone')->label('Phone')->tel(),

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
            ->title('Customer updated successfully')
            ->body(" The customer {$this->record->name} has been updated successfully.")
            ->success()
            ->send();
        redirect()->route('customers.index');
    }

    public function render(): View
    {
        return view('livewire.customers.edit-customer');
    }
}
