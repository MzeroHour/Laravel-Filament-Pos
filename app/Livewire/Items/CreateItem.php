<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Component;

class CreateItem extends Component implements HasActions, HasSchemas
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
                Section::make('Create Item')
                    ->description('Create a new item with all the details of the item.')
                    ->columns(2)
                    ->schema([
                        // ...
                        TextInput::make('name')->label('Item Name')->required(),
                        TextInput::make('sku')->label('SKU')->required(),
                        TextInput::make('price')->label('Price')->numeric()->prefix('$')->required(),

                        ToggleButtons::make('status')
                            ->label('Is this item active?')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->grouped()
                    ])
            ])
            ->statePath('data')
            ->model(Item::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        try {
            $record = Item::create($data);
            $this->form->model($record)->saveRelationships();

            Notification::make()
                ->title('Item created successfully')
                ->body(" The item {$record->name} has been created successfully.")
                ->success()
                ->send();
            redirect()->route('items.index');
        } catch (QueryException $exception) {
            if (isset($exception->errorInfo[1]) && $exception->errorInfo[1] === 1062) {
                Notification::make()
                    ->title('Duplicate SKU')
                    ->body('The SKU provided already exists. Please enter a unique SKU.')
                    ->danger()
                    ->send();

                return;
            }

            throw $exception;
        }
    }

    public function render(): View
    {
        return view('livewire.items.create-item');
    }
}
