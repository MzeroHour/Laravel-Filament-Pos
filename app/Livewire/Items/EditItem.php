<?php

namespace App\Livewire\Items;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Contracts\View\View;
use App\Models\Item;
use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\QueryException;
use Livewire\Component;

class EditItem extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Item $record;

    public ?array $data = [];

    public function mount(): void
    {
        // it populate the form with the record data when the component is mounted
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
                        TextInput::make('name')->label('Item Name'),
                        TextInput::make('sku')->label('SKU'),
                        TextInput::make('price')->label('Price')->numeric()->prefix('$'),

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
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        try {
            $this->record->update($data);
            Notification::make()
                ->title('Item updated successfully')
                ->body(" The item {$this->record->name} has been updated successfully.")
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


        //  

    }

    public function render(): View
    {
        return view('livewire.items.edit-item');
    }
}
