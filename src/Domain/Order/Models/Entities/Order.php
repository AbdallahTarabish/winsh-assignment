<?php

declare(strict_types=1);

namespace Src\Domain\Order\Models\Entities;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Domain\Order\Enums\OrderStatus;

/**
 * @property int $id
 * @property string $customer_name
 * @property OrderStatus $status
 * @property float $pickup_lat
 * @property float $pickup_lng
 * @property int|null $driver_id
 * @property \Illuminate\Support\Carbon|null $assigned_at
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_name',
        'status',
        'pickup_lat',
        'pickup_lng',
        'driver_id',
        'assigned_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'assigned_at' => 'datetime',
    ];

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
