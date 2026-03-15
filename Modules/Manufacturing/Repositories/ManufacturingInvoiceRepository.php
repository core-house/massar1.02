<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Repositories;

use App\Models\OperHead;
use Illuminate\Support\Facades\DB;

class ManufacturingInvoiceRepository
{
    /**
     * Get statistics for manufacturing invoices
     */
    public function getStatistics(): array
    {
        $query = OperHead::where('pro_type', 59);

        $total = $query->count();
        $totalValue = $query->sum('pro_value');
        $avgValue = $total > 0 ? round($totalValue / $total, 2) : 0;

        // This month statistics
        $thisMonth = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y'))
            ->whereMonth('pro_date', date('m'))
            ->count();

        return [
            'total' => $total,
            'totalValue' => $totalValue,
            'avgValue' => $avgValue,
            'thisMonth' => $thisMonth,
        ];
    }

    /**
     * Delete an invoice
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $invoice = OperHead::findOrFail($id);

            // Delete related items (use operationItems not items)
            $invoice->operationItems()->delete();

            // Delete related journal entries
            if ($invoice->journalHead) {
                // Use dets() not journalDetails()
                $invoice->journalHead->dets()->delete();
                $invoice->journalHead->delete();
            }

            // Delete invoice
            $invoice->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
