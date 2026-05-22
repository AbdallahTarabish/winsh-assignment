<?php

declare(strict_types=1);

namespace Src\Domain\Dispatch\DataTransferObjects;

final readonly class AssignmentResult
{
    public function __construct(
        public int $orderId,
        public int $driverId,
        public ?string $driverName,
        public ?float $distanceKm,
        public bool $alreadyAssigned,
    ) {}

    public static function assigned(int $orderId, int $driverId, ?string $driverName, ?float $distanceKm): self
    {
        return new self($orderId, $driverId, $driverName, $distanceKm, false);
    }

    public static function alreadyAssigned(int $orderId, int $driverId, ?string $driverName): self
    {
        return new self($orderId, $driverId, $driverName, null, true);
    }
}
