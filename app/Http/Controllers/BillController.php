<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Get bill details.
     */
    public function getBill($billId)
    {
        $bill = Bill::with('visit.patient', 'visit.doctor.doctorProfile', 'items')
            ->find($billId);

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Bill not found'], 404);
        }

        return response()->json([
            'success' => true,
            'bill' => [
                'id' => $bill->id,
                'visit_id' => $bill->visit_id,
                'medicine_total' => (float)$bill->medicine_total,
                'doctor_charges_total' => (float)$bill->doctor_charges_total,
                'taxes' => (float)$bill->taxes,
                'total_amount' => (float)$bill->total_amount,
                'paid_amount' => (float)$bill->paid_amount,
                'status' => $bill->status,
                'items' => $bill->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->item_type,
                        'title' => $item->title,
                        'qty' => $item->qty,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
                'created_at' => $bill->created_at,
            ],
        ]);
    }

    /**
     * View invoice (HTML).
     */
    public function viewInvoice($billId)
    {
        $bill = Bill::with(
            'visit.patient',
            'visit.doctor.doctorProfile',
            'items'
        )->find($billId);

        if (!$bill) {
            abort(404);
        }

        // Auto-pay the bill if it's not already paid
        if ($bill->getBalanceDue() > 0) {
            $bill->paid_amount = $bill->total_amount;
            $bill->status = 'paid';
            $bill->save();
        }

        $doctorProfile = $bill->visit->doctor->doctorProfile ?? null;

        return view('bills.invoice', [
            'bill' => $bill,
            'doctorProfile' => $doctorProfile,
        ]);
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadInvoicePdf($billId)
    {
        $bill = Bill::with(
            'visit.patient',
            'visit.doctor.doctorProfile',
            'items'
        )->find($billId);

        if (!$bill) {
            abort(404);
        }

        $doctorProfile = $bill->visit->doctor->doctorProfile ?? null;

        $pdf = Pdf::loadView('bills.invoice-pdf', [
            'bill' => $bill,
            'doctorProfile' => $doctorProfile,
        ]);

        return $pdf->download("invoice-{$bill->id}.pdf");
    }

    /**
     * Record a payment for a bill.
     *
     * Request:
     * {
     *   "amount": 100.00
     * }
     */
    public function recordPayment(Request $request, $billId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $bill = Bill::find($billId);

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Bill not found'], 404);
        }

        $newPaidAmount = $bill->paid_amount + $validated['amount'];

        // Determine status
        if ($newPaidAmount >= $bill->total_amount) {
            $status = 'paid';
            $paidAmount = $bill->total_amount;
        } elseif ($newPaidAmount > 0) {
            $status = 'partial';
            $paidAmount = $newPaidAmount;
        } else {
            $status = 'unpaid';
            $paidAmount = 0;
        }

        $bill->update([
            'paid_amount' => $paidAmount,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'bill' => [
                'id' => $bill->id,
                'total_amount' => (float)$bill->total_amount,
                'paid_amount' => (float)$bill->paid_amount,
                'balance_due' => (float)$bill->getBalanceDue(),
                'status' => $bill->status,
            ],
        ]);
    }
}
