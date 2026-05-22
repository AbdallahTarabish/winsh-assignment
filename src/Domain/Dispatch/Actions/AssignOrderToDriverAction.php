<?php

declare(strict_types=1);

namespace Src\Domain\Dispatch\Actions;

use Illuminate\Support\Facades\DB;
use Src\Domain\Dispatch\Contracts\OrderAssignmentContract;
use Src\Domain\Dispatch\DataTransferObjects\AssignmentResult;
use Src\Domain\Dispatch\Exceptions\NoDriverAvailableException;
use Src\Domain\Driver\Contracts\DriverContract;
use Src\Domain\Driver\DataTransferObjects\AvailableDriver;
use Src\Domain\Order\Contracts\OrderContract;
use Src\Domain\Order\Enums\OrderStatus;
use Src\Domain\Order\Exceptions\OrderNotAssignableException;
use Src\Domain\Order\Exceptions\OrderNotFoundException;

class AssignOrderToDriverAction implements OrderAssignmentContract
{
    private const CANDIDATE_BATCH = 10;

    public function __construct(
        private readonly OrderContract $orders,
        private readonly DriverContract $drivers,
    ) {}

    public function assign(int $orderId): AssignmentResult
    {
        return DB::transaction(function () use ($orderId): AssignmentResult {
            $order = $this->orders->lockForAssignment($orderId);

            if ($order === null) {
                throw OrderNotFoundException::withId($orderId);
            }

            //  already assigned -> return the existing binding.
            if ($order->status === OrderStatus::Assigned && $order->driverId !== null) {
                return $this->existingAssignment($orderId, $order->driverId);
            }

            if ($order->status !== OrderStatus::Pending) {
                throw new OrderNotAssignableException($orderId, $order->status);
            }

            return $this->assignNearestAvailable($orderId, $order->pickupLat, $order->pickupLng);
        });
    }

    /**
     * Probe outward, one batch of nearby drivers at a time, claiming the first
     * usable one.
     *
     */

    private function assignNearestAvailable(int $orderId, float $lat, float $lng): AssignmentResult
    {
        $excluded = [];

        while (($candidates = $this->drivers->nearestOnlineWithin($lat, $lng, self::CANDIDATE_BATCH, $excluded)) !== []) {
            $busy = array_flip($this->orders->busyAmong(
                array_map(fn (AvailableDriver $d): int => $d->id, $candidates)
            ));

            foreach ($candidates as $candidate) {
                if (! isset($busy[$candidate->id])
                    && ($result = $this->claim($orderId, $candidate)) !== null) {
                    return $result;
                }

                $excluded[] = $candidate->id;
            }
        }

        throw NoDriverAvailableException::forOrder($orderId);
    }

    /**
     * Lock the candidate's row and, under that lock, confirm it still has no
     * active order before binding it.     */
    private function claim(int $orderId, AvailableDriver $candidate): ?AssignmentResult
    {
        $locked = $this->drivers->lockOnlineById($candidate->id);

        if ($locked === null || $this->orders->driverHasActiveOrder($candidate->id)) {
            return null;
        }

        $this->orders->assignToDriver($orderId, $candidate->id);

        return AssignmentResult::assigned($orderId, $candidate->id, $candidate->name, $candidate->distanceKm);
    }

    private function existingAssignment(int $orderId, int $driverId): AssignmentResult
    {
        $driver = $this->drivers->findById($driverId);

        return AssignmentResult::alreadyAssigned($orderId, $driverId, $driver?->name);
    }
}
