<?php

use App\Livewire\Customers\CreateCustomer;
use App\Livewire\Customers\EditCustomer;
use App\Livewire\Customers\ListCustomers;
use App\Livewire\Inventories\CreateInventory;
use App\Livewire\Inventories\EditInventory;
use App\Livewire\Inventories\ListInventories;
use App\Livewire\Items\CreateItem;
use App\Livewire\Items\EditItem;
use App\Livewire\Items\ListItems;
use App\Livewire\Payments\CreatePaymentMethod;
use App\Livewire\Payments\EditPaymentMethod;
use App\Livewire\Payments\ListPaymentMethods;
use App\Livewire\Sales\EditSale;
use App\Livewire\Sales\ListSales;
use App\Livewire\Users\CreateUser;
use App\Livewire\Users\EditUser;
use App\Livewire\Users\ListUsers;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Management Routes with Livewire Components and Filament Tables
    Route::get('/manage-users', ListUsers::class)->name('users.index');
    Route::get('/edit-users/{record}', EditUser::class)->name('users.edit');
    Route::get('/create-users', CreateUser::class)->name('users.create');

    //item routes
    Route::get('/manage-items', ListItems::class)->name('items.index');
    Route::get('/create-items', CreateItem::class)->name('items.create');
    Route::get('/edit-items/{record}', EditItem::class)->name('items.edit');

    //inventory routes
    Route::get('/manage-inventories', ListInventories::class)->name('inventories.index');
    Route::get('/edit-inventories/{record}', EditInventory::class)->name('inventories.edit');
    Route::get('/create-inventories', CreateInventory::class)->name('inventories.create');

    //sales routes
    Route::get('/manage-sales', ListSales::class)->name('sales.index');
    Route::get('/edit-sales/{record}', EditSale::class)->name('sales.edit');


    //customer routes
    Route::get('/manage-customers', ListCustomers::class)->name('customers.index');
    Route::get('/edit-customers/{record}', EditCustomer::class)->name('customers.edit');
    Route::get('/create-customers', CreateCustomer::class)->name('customers.create');

    //payment routes
    Route::get('/manage-payments', ListPaymentMethods::class)->name('payments.index');
    Route::get('/edit-payment-methods/{record}', EditPaymentMethod::class)->name('payments.edit');
    Route::get('/create-payment-methods', CreatePaymentMethod::class)->name('payments.create');
});

require __DIR__ . '/settings.php';
