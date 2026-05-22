<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Domain\Order\Models\Entities\Order;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'status' => $this->status->value,
            'pickup' => [
                'lat' => $this->pickup_lat,
                'lng' => $this->pickup_lng,
            ],
            'driver_id' => $this->driver_id,
            'assigned_at' => $this->assigned_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
