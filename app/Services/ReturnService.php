<?php
namespace App\Services;

use App\Models\OrderReturn;
use App\Models\ReturnItem;
use App\Events\ReturnProcessed;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * Approve a return request.
     */
    public function approve(OrderReturn $return): OrderReturn
    {
        $return->update(['status' => 'approved']);
        return $return->fresh();
    }

    /**
     * Mark return as received at warehouse.
     */
    public function markReceived(OrderReturn $return, int $warehouseId): OrderReturn
    {
        $return->update([
            'status'      => 'received',
            'warehouse_id'=> $warehouseId,
            'received_at' => now(),
        ]);
        return $return->fresh();
    }

    /**
     * Perform QC on received return.
     */
    public function performQC(OrderReturn $return, string $qcStatus, string $disposition, ?string $notes = null): OrderReturn
    {
        $return->update([
            'qc_status'   => $qcStatus,
            'disposition' => $disposition,
            'qc_notes'    => $notes,
            'qc_at'       => now(),
            'status'      => 'qc_complete',
        ]);
        return $return->fresh();
    }

    /**
     * Process disposition — restock, scrap, or replace.
     */
    public function processDisposition(OrderReturn $return): OrderReturn
    {
        return DB::transaction(function () use ($return) {
            if ($return->disposition === 'restock') {
                foreach ($return->items as $item) {
                    $this->inventory->restock(
                        $item->product_id,
                        $return->warehouse_id,
                        $item->quantity,
                        'Return',
                        $return->id,
                        null,
                        "Restock from Return #{$return->return_number}"
                    );
                }
                $return->update([
                    'status'       => 'restocked',
                    'restocked_at' => now(),
                ]);
            } elseif ($return->disposition === 'scrap') {
                $return->update(['status' => 'scrapped']);
            } elseif ($return->disposition === 'replace') {
                $return->update(['status' => 'replace_initiated']);
            }

            // Update related order status
            if ($return->order) {
                $return->order->update(['status' => 'return_completed']);
            }

            event(new ReturnProcessed($return));

            return $return->fresh();
        });
    }
}
