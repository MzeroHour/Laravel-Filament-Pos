<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Inventory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Sale;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Log;

class EditSale extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Sale $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->record->load(['saleItems.item', 'customer', 'paymentMethod']);

        $this->form->fill([
            'customer_id' => $this->record->customer_id,
            'payment_method_id' => $this->record->payment_method_id,
            'total_amount' => $this->record->total_amount,
            'paid_amount' => $this->record->paid_amount,
            'discount_amount' => $this->record->discount_amount,
            'tax_amount' => $this->record->tax_amount,
            'is_paid' => $this->record->is_paid,
            'sale_items' => $this->record->saleItems->map(function ($saleItem) {
                return [
                    'id' => $saleItem->id,
                    'item_id' => $saleItem->item_id,
                    'item_name' => $saleItem->item->name,
                    'quantity' => $saleItem->quantity,
                    'unit_price' => $saleItem->unit_price,
                    'total_amount' => $saleItem->total_amount,
                ];
            })->toArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Sale Details")
                    ->description('Update the details of the sale.')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->label('Customer')
                            ->options(Customer::query()->pluck('name', 'id'))
                            ->searchable(),
                        Select::make('payment_method_id')
                            ->relationship('paymentMethod', 'name')
                            ->label('Payment Method')
                            ->options(PaymentMethodModel::query()->pluck('name', 'id'))
                            ->searchable(),
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('paid_amount')
                            ->label('Paid Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),
                        Toggle::make('is_paid')
                            ->label('Is Paid')
                            ->default(false),
                    ]),
                Section::make("Sale Items")
                    ->description('Items included in this sale.')
                    ->schema([
                        Repeater::make('sale_items')
                            ->label('Items')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->options(Item::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $item = Item::find($state);
                                        if ($item) {
                                            $set('unit_price', $item->price);
                                            $set('item_name', $item->name);
                                        }
                                    }),
                                // Placeholder::make('item_name_preview')
                                //     ->label('Item Name')
                                //     ->content(fn($get) => $get('item_name') ?? 'Select an item'),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitPrice = $get('unit_price') ?: 0;
                                        $quantity = $state ?: 0;
                                        $set('total_amount', $unitPrice * $quantity);
                                    }),
                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = $get('quantity') ?: 0;
                                        $unitPrice = $state ?: 0;
                                        $set('total_amount', $unitPrice * $quantity);
                                    }),
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Validate inventory availability for new/changed items
        foreach ($data['sale_items'] as $saleItemData) {
            $inventory = Inventory::where('item_id', $saleItemData['item_id'])->first();

            if (isset($saleItemData['id'])) {
                // Existing sale item - check if quantity change is valid
                $existingSaleItem = $this->record->saleItems()->find($saleItemData['id']);
                if ($existingSaleItem) {
                    $quantityDifference = $saleItemData['quantity'] - $existingSaleItem->quantity;
                    if ($quantityDifference > 0 && (!$inventory || $inventory->quantity < $quantityDifference)) {
                        Notification::make()
                            ->title('Insufficient inventory')
                            ->body("Not enough stock for item. Available: " . ($inventory ? $inventory->quantity : 0))
                            ->danger()
                            ->send();
                        return;
                    }
                }
            } else {
                // New sale item - check if enough inventory
                if (!$inventory || $inventory->quantity < $saleItemData['quantity']) {
                    Notification::make()
                        ->title('Insufficient inventory')
                        ->body("Not enough stock for item. Available: " . ($inventory ? $inventory->quantity : 0))
                        ->danger()
                        ->send();
                    return;
                }
            }
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get original sale items for inventory adjustment
            $originalSaleItems = $this->record->saleItems->keyBy('id');

            // Update the sale
            $this->record->update([
                'customer_id' => $data['customer_id'],
                'payment_method_id' => $data['payment_method_id'],
                'paid_amount' => $data['paid_amount'],
                'discount_amount' => $data['discount_amount'],
                'is_paid' => $data['is_paid'],
            ]);

            // Get existing sale item IDs
            $existingSaleItemIds = $this->record->saleItems->pluck('id')->toArray();

            // Process sale items
            $updatedSaleItemIds = [];
            $totalAmount = 0;

            foreach ($data['sale_items'] as $saleItemData) {
                $saleItemTotalAmount = $saleItemData['total_amount'] ?? ($saleItemData['quantity'] * $saleItemData['unit_price']);
                $totalAmount += $saleItemTotalAmount;

                // Check if this is an existing sale item (has an ID) or new
                if (isset($saleItemData['id']) && in_array($saleItemData['id'], $existingSaleItemIds)) {
                    // Update existing sale item
                    $saleItem = $this->record->saleItems()->find($saleItemData['id']);
                    if ($saleItem) {
                        $originalQuantity = $saleItem->quantity;
                        $newQuantity = $saleItemData['quantity'];

                        $saleItem->update([
                            'item_id' => $saleItemData['item_id'],
                            'quantity' => $newQuantity,
                            'unit_price' => $saleItemData['unit_price'],
                            'total_amount' => $saleItemTotalAmount,
                        ]);

                        // Adjust inventory: if quantity increased, reduce inventory; if decreased, increase inventory
                        $quantityDifference = $newQuantity - $originalQuantity;
                        if ($quantityDifference != 0) {
                            $inventory = Inventory::where('item_id', $saleItemData['item_id'])->first();
                            if ($inventory) {
                                $inventory->quantity -= $quantityDifference; // Subtract the difference (negative difference increases inventory)
                                $inventory->save();
                            }
                        }

                        $updatedSaleItemIds[] = $saleItem->id;
                    }
                } else {
                    // Create new sale item
                    $newSaleItem = $this->record->saleItems()->create([
                        'item_id' => $saleItemData['item_id'],
                        'quantity' => $saleItemData['quantity'],
                        'unit_price' => $saleItemData['unit_price'],
                        'total_amount' => $saleItemTotalAmount,
                    ]);

                    // Reduce inventory for new sale item
                    $inventory = Inventory::where('item_id', $saleItemData['item_id'])->first();
                    if ($inventory) {
                        $inventory->quantity -= $saleItemData['quantity'];
                        $inventory->save();
                    }

                    $updatedSaleItemIds[] = $newSaleItem->id;
                }
            }

            // Delete sale items that are no longer in the form
            $saleItemsToDelete = array_diff($existingSaleItemIds, $updatedSaleItemIds);
            if (!empty($saleItemsToDelete)) {
                foreach ($saleItemsToDelete as $saleItemId) {
                    $saleItemToDelete = $originalSaleItems[$saleItemId] ?? null;
                    if ($saleItemToDelete) {
                        // Return inventory for deleted sale items
                        $inventory = Inventory::where('item_id', $saleItemToDelete->item_id)->first();
                        if ($inventory) {
                            $inventory->quantity += $saleItemToDelete->quantity;
                            $inventory->save();
                        }
                    }
                }

                $this->record->saleItems()->whereIn('id', $saleItemsToDelete)->delete();
            }

            // Calculate tax (15% of subtotal)
            $subtotal = $totalAmount;
            $taxAmount = $subtotal * 0.15;
            $finalTotal = $subtotal + $taxAmount - $data['discount_amount'];

            // Update the total amount
            $this->record->update([
                'total_amount' => $finalTotal,
                'tax_amount' => $taxAmount,
            ]);

            DB::commit();

            // Refresh the form data
            $this->record->refresh();
            $this->record->load(['saleItems.item', 'customer', 'paymentMethod']);

            $this->form->fill([
                'customer_id' => $this->record->customer_id,
                'payment_method_id' => $this->record->payment_method_id,
                'total_amount' => $this->record->total_amount,
                'paid_amount' => $this->record->paid_amount,
                'discount_amount' => $this->record->discount_amount,
                'tax_amount' => $this->record->tax_amount,
                'is_paid' => $this->record->is_paid,
                'sale_items' => $this->record->saleItems->map(function ($saleItem) {
                    return [
                        'id' => $saleItem->id,
                        'item_id' => $saleItem->item_id,
                        'item_name' => $saleItem->item->name,
                        'quantity' => $saleItem->quantity,
                        'unit_price' => $saleItem->unit_price,
                        'total_amount' => $saleItem->total_amount,
                    ];
                })->toArray(),
            ]);

            Notification::make()
                ->title('Sale updated successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollback();

            // Log the actual error for debugging
            Log::error('Failed to update sale: ' . $e->getMessage(), [
                'sale_id' => $this->record->id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Failed to update sale')
                ->body('An error occurred while updating the sale: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.sales.edit-sale');
    }
}
