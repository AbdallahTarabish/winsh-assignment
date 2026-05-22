<?php

declare(strict_types=1);

namespace Src\Domain\Order\DataTransferObjects;

use Src\Domain\Order\Enums\OrderStatus;

final readonly class OrderSnapshot
{
    public function __construct(
        public int $id,
        public OrderStatus $status,
        public ?int $driverId,
        public float $pickupLat,
        public float $pickupLng,
    ) {}
}
