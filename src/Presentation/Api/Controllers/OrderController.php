<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Src\Domain\Dispatch\Contracts\OrderAssignmentContract;
use Src\Domain\Order\Contracts\OrderContract;
use Src\Presentation\Api\Requests\AssignOrderRequest;
use Src\Presentation\Api\Requests\IndexOrdersRequest;
use Src\Presentation\Api\Resources\AssignmentResource;
use Src\Presentation\Api\Resources\OrderResource;

class OrderController
{
    public function index(IndexOrdersRequest $request, OrderContract $orders): AnonymousResourceCollection
    {
        return OrderResource::collection(
            $orders->paginate($request->statusFilter(), $request->perPage())
        );
    }

    public function assign(AssignOrderRequest $request, string $order, OrderAssignmentContract $assigner): AssignmentResource
    {
        return new AssignmentResource($assigner->assign((int) $order));
    }
}
