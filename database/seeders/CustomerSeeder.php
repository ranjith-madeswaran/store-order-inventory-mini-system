<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()->createMany([
            ['name' => 'Alice Smith', 'email' => 'alice@example.com'],
            ['name' => 'Bob Jones', 'email' => 'bob@example.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@example.com'],
        ]);

        Customer::factory(5)->create();
    }
}
