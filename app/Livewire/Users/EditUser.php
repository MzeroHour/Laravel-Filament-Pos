<?php

namespace App\Livewire\Users;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;

class EditUser extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public User $record;

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
                Section::make('Edit user')
                    ->description('update the user details as you wish!!')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('email')
                            ->unique(ignoreRecord: true),
                        Select::make('role')
                            ->options([
                                'cashier' => 'Cashier',
                                'admin' => 'Admin',
                                'other' => 'Other',
                            ])
                            ->native(false),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state)) // Only saves to DB if the field is NOT empty
                            ->required(fn(string $context): bool => $context === 'create') // Only required when creating a new user
                            ->revealable() // Adds an eye icon to show/hide what is being typed

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
            ->title('User updated successfully')
            ->body(" The user {$this->record->name} has been updated successfully.")
            ->success()
            ->send();
        redirect()->route('users.index');
    }

    public function render(): View
    {
        return view('livewire.users.edit-user');
    }
}
