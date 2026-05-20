<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Domain\Order\Enums\OrderStatus;
use Src\Domain\Order\Models\Entities\Order;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    // Pickups land around the same Riyadh area as the drivers.
    private const CENTER_LAT = 24.7136;

    private const CENTER_LNG = 46.6753;

    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'status' => OrderStatus::Pending,
            'pickup_lat' => self::CENTER_LAT + fake()->randomFloat(5, -0.08, 0.08),
            'pickup_lng' => self::CENTER_LNG + fake()->randomFloat(5, -0.08, 0.08),
            'driver_id' => null,
            'assigned_at' => null,
        ];
    }
}
