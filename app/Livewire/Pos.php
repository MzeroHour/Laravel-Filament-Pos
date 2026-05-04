<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Item;
use App\Models\PaymentMethod;
use Illuminate\Database\Query\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Pos extends Component
{

    public $items;
    public $customers;
    public $paymentMethods;
    public $search = '';
    public $cart = [];

    public $customer_id = null;
    public $payment_method_id = null;
    public $total_amount = 0;
    public $paid_amount = 0;
    public $discount_amount = 0;
    public $tax_amount = 0;
    public $change_amount = 0;

    public function mount()
    {
        $this->items = Item::whereHas('inventory', function ($builder) {
            $builder->where('quantity', '>', 0);
        })
            ->with('inventory')
            ->where('status', 'active')
            ->get();
        $this->customers = Customer::all();
        $this->paymentMethods = PaymentMethod::all();

        // dd($this->items, $this->customers, $this->paymentMethods);
    }

    #[Computed()]
    public function filteredItems()
    {
        if (empty($this->search)) {
            return $this->items;
        }
        return $this->items->filter(function ($item) {
            return str_contains(strtolower($item->name), strtolower($this->search))
                || str_contains(strtolower($item->sku), strtolower($this->search));
        });
    }


    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    //placeholder for tax
    #[Computed]
    public function tax()
    {
        return $this->subtotal * 0.15; // 15%
    }

    #[Computed]
    public function totalBeforeDiscount()
    {
        return $this->subtotal + $this->tax;
    }

    #[Computed]
    public function total()
    {
        $discountedTotal = $this->totalBeforeDiscount - $this->discount_amount;

        return $discountedTotal;
    }

    #[Computed]
    public function change()
    {
        if ($this->paid_amount > $this->total) {
            return $this->paid_amount - $this->total;
        }
        return 0;
    }


    public function render()
    {

        return view('livewire.pos');
    }
}
