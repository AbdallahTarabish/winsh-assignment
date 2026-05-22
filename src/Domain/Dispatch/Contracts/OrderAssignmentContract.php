<?php

declare(strict_types=1);

namespace Src\Domain\Dispatch\Contracts;

use Src\Domain\Dispatch\DataTransferObjects\AssignmentResult;

interface OrderAssignmentContract
{
    public function assign(int $orderId): AssignmentResult;
}
