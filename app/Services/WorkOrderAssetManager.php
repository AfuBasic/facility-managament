<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Space;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAsset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkOrderAssetManager
{
    /**
     * Reserve an asset for a work order
     */
    public function reserve(WorkOrder $workOrder, Asset $asset, int $quantity, User $user, ?string $note = null): void
    {
        WorkOrderAsset::create([
            'work_order_id' => $workOrder->id,
            'asset_id' => $asset->id,
            'action' => 'reserved',
            'quantity' => $quantity,
            'user_id' => $user->id,
            'performed_at' => now(),
            'note' => $note,
        ]);
    }

    /**
     * Consume an asset (decrements inventory)
     */
    public function consume(WorkOrder $workOrder, Asset $asset, int $quantity, User $user, ?string $note = null): void
    {
        DB::transaction(function () use ($workOrder, $asset, $quantity, $user, $note) {
            // Record consumption
            WorkOrderAsset::create([
                'work_order_id' => $workOrder->id,
                'asset_id' => $asset->id,
                'action' => 'consumed',
                'quantity' => $quantity,
                'user_id' => $user->id,
                'performed_at' => now(),
                'note' => $note,
            ]);

            // Decrement asset inventory
            $asset->decrement('units', $quantity);
        });
    }

    /**
     * Check out an asset (tool)
     */
    public function checkOut(WorkOrder $workOrder, Asset $asset, int $quantity, User $user): void
    {
        WorkOrderAsset::create([
            'work_order_id' => $workOrder->id,
            'asset_id' => $asset->id,
            'action' => 'checked_out',
            'quantity' => $quantity,
            'user_id' => $user->id,
            'performed_at' => now(),
        ]);
    }

    /**
     * Check in an asset (return tool)
     */
    public function checkIn(WorkOrder $workOrder, Asset $asset, int $quantity, User $user): void
    {
        WorkOrderAsset::create([
            'work_order_id' => $workOrder->id,
            'asset_id' => $asset->id,
            'action' => 'checked_in',
            'quantity' => $quantity,
            'user_id' => $user->id,
            'performed_at' => now(),
        ]);
    }

    /**
     * Release unused reservation
     */
    public function release(WorkOrder $workOrder, Asset $asset, int $quantity, User $user): void
    {
        WorkOrderAsset::create([
            'work_order_id' => $workOrder->id,
            'asset_id' => $asset->id,
            'action' => 'released',
            'quantity' => $quantity,
            'user_id' => $user->id,
            'performed_at' => now(),
        ]);
    }

    /**
     * Install an asset at a location
     */
    public function install(WorkOrder $workOrder, Asset $asset, int $quantity, User $user, Space $location): void
    {
        DB::transaction(function () use ($workOrder, $asset, $quantity, $user, $location) {
            // Record installation
            WorkOrderAsset::create([
                'work_order_id' => $workOrder->id,
                'asset_id' => $asset->id,
                'action' => 'installed',
                'quantity' => $quantity,
                'user_id' => $user->id,
                'performed_at' => now(),
                'note' => "Installed at {$location->name}",
            ]);

            // Update asset location
            $asset->update(['space_id' => $location->id]);
        });
    }

    /**
     * Get reserved assets for a work order
     */
    public function getReservedAssets(WorkOrder $workOrder): Collection
    {
        return WorkOrderAsset::where('work_order_id', $workOrder->id)
            ->where('action', 'reserved')
            ->with('asset')
            ->get();
    }

    /**
     * Get used assets for a work order
     */
    public function getUsedAssets(WorkOrder $workOrder): Collection
    {
        return WorkOrderAsset::where('work_order_id', $workOrder->id)
            ->whereIn('action', ['consumed', 'checked_out', 'installed'])
            ->with('asset')
            ->get();
    }

    /**
     * Reconcile reserved vs. used assets
     */
    public function reconcile(WorkOrder $workOrder): array
    {
        $reserved = $this->getReservedAssets($workOrder);
        $used = $this->getUsedAssets($workOrder);

        $summary = [];

        foreach ($reserved as $reservation) {
            $assetId = $reservation->asset_id;
            $reservedQty = $reservation->quantity;

            $usedQty = $used->where('asset_id', $assetId)->sum('quantity');

            $summary[] = [
                'asset' => $reservation->asset,
                'reserved' => $reservedQty,
                'used' => $usedQty,
                'variance' => $usedQty - $reservedQty,
            ];
        }

        return $summary;
    }
}
