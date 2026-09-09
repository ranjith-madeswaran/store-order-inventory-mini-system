<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => 'PRD-' . fake()->unique()->numerify('#####'),
            'price' => fake()->randomFloat(2, 5, 500),
            'tax_percentage' => fake()->randomElement([0.00, 5.00, 10.00, 18.00]),
            'stock' => fake()->numberBetween(15, 100),
        ];
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock(int $stock = 2): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
