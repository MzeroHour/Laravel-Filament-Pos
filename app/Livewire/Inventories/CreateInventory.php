<?php

namespace App\Livewire\Inventories;

use App\Models\Inventory;
use App\Models\Item;
use Dom\Text;
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

use function Laravel\Prompts\select;

class CreateInventory extends Component implements HasActions, HasSchemas
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
                Section::make('Create Inventory')
                    ->description('Create a new inventory with all the details of the inventory.')
                    ->columns(2)
                    ->schema([
                        // ...
                        Select::make('item_id')
                            ->relationship('item', 'name')
                            ->label('Select Item')
                            ->options(Item::query()->pluck('name', 'id'))
                            ->searchable()->required(),
                        TextInput::make('quantity')->label('Quantity')->numeric()->required(),
                    ]),
            ])
            ->statePath('data')
            ->model(Inventory::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Inventory::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()
            ->title('Inventory Created')
            ->body('The inventory has been created successfully.')
            ->success()
            ->send();
        redirect()->route('inventories.index');
    }

    public function render(): View
    {
        return view('livewire.inventories.create-inventory');
    }
}
