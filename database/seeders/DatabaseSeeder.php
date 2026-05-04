<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\PaymentMethod;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Item::factory(100)->create();
        // Inventory::factory(20)->create();
        // Customer::factory(15)->create();
        PaymentMethod::factory(5)->create();
    }
}
