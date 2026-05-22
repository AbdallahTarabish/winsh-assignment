<?php

declare(strict_types=1);

namespace Src\Domain\Driver\DataTransferObjects;

final readonly class AvailableDriver
{
    public function __construct(
        public int $id,
        public string $name,
        public ?float $distanceKm = null,
    ) {}
}
