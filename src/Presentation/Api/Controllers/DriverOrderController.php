<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Src\Domain\Order\Contracts\OrderContract;
use Src\Presentation\Api\Requests\DriverOrdersRequest;
use Src\Presentation\Api\Resources\OrderResource;

class DriverOrderController
{
    public function index(DriverOrdersRequest $request, string $driver, OrderContract $orders): AnonymousResourceCollection
    {
        return OrderResource::collection(
            $orders->paginateForDriver((int) $driver, $request->statusFilter(), $request->perPage())
        );
    }
}
