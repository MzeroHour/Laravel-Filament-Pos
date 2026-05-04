<?php

namespace App\Livewire\Users;

use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateUser extends Component implements HasActions, HasSchemas
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
                Section::make('Create User')
                    ->description('Create a new user with all the details of the user.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('email')->label('Email')->email()->required(),
                        TextInput::make('password')->label('Password')->password()->required()->revealable(),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'cashier' => 'Cashier',
                                'user' => 'User',
                                'other' => 'Other',
                            ])->native(false)->searchable()->required(),
                    ])
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()
            ->title('User Created')
            ->body('The user has been created successfully.')
            ->success()
            ->send();
        $this->redirect(route('users.index'));
    }

    public function render(): View
    {
        return view('livewire.users.create-user');
    }
}
