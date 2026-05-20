<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Domain\Driver\Enums\DriverStatus;
use Src\Domain\Driver\Models\Entities\Driver;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    // ---- Riyadh city drivers are scattered within a few km ---//
    private const CENTER_LAT = 24.7136;
    private const CENTER_LNG = 46.6753;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'status' => DriverStatus::Online,
            'lat' => self::CENTER_LAT + fake()->randomFloat(5, -0.05, 0.05),
            'lng' => self::CENTER_LNG + fake()->randomFloat(5, -0.05, 0.05),
        ];
    }

    public function offline(): static
    {
        return $this->state(fn () => ['status' => DriverStatus::Offline]);
    }
}
