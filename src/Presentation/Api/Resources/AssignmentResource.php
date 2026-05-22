<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Domain\Dispatch\DataTransferObjects\AssignmentResult;

/**
 * @mixin AssignmentResult
 */
class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->orderId,
            'driver_id' => $this->driverId,
            'driver_name' => $this->driverName,
            'distance_km' => $this->distanceKm,
            'already_assigned' => $this->alreadyAssigned,
        ];
    }
}
