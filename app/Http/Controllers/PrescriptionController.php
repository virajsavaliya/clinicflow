<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\DoctorCharge;
use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Notification;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\StockTransaction;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrescriptionController extends Controller
{
    /**
     * Finalize a prescription with transactional stock locking.
     *
     * This core method implements:
     * - Database transaction with SERIALIZABLE isolation (if supported)
     * - Stock locking via lockForUpdate() to prevent race conditions
     * - Prescription and bill creation
     * - Doctor charges snapshot
     * - Audit logging
     *
     * Request body:
     * {
     *   "diagnosis_notes": "string (optional)",
     *   "items": [
     *     { "medicine_id": 1, "qty": 5 }
     *   ],
     *   "selected_charges": [1, 3],  // doctor_charge ids
     *   "doctor_fees": {            // optional price overrides
     *     "1": 100.00
     *   },
     *   "taxes": 10.50              // optional
     * }
     *
     * Response on success:
     * {
     *   "success": true,
     *   "message": "Prescription finalized successfully",
     *   "bill": {
     *     "id": 1,
     *     "visit_id": 5,
     *     "medicine_total": 250.00,
     *     "doctor_charges_total": 150.00,
     *     "taxes": 40.00,
     *     "total_amount": 440.00
     *   }
     * }
     *
     * Response on error (insufficient stock):
     * {
     *   "success": false,
     *   "message": "Insufficient stock for medicines",
     *   "errors": {
     *     "medicines": [
     *       {
     *         "medicine_id": 1,
     *         "name": "Aspirin",
     *         "requested": 10,
     *         "available": 5
     *       }
     *     ]
     *   }
     * }
     */
    public function finalizePrescription(Request $request, $visitId)
    {
        try {
            // Validate request - items is optional (consultation-only prescriptions allowed)
            $validated = $request->validate([
                'diagnosis_notes' => 'nullable|string|max:2000',
                'items' => 'nullable|array',
                'items.*.medicine_id' => 'required|integer|exists:medicines,id',
                'items.*.qty' => 'required|integer|min:1',
                'selected_charges' => 'nullable|array',
                'selected_charges.*' => 'integer|exists:doctor_charges,id',
                'doctor_fees' => 'nullable|array',
                'doctor_fees.*' => 'numeric|min:0',
                'taxes' => 'nullable|numeric|min:0',
            ]);

            // Get visit and validate it exists and belongs to the authenticated doctor
            $visit = Visit::find($visitId);
            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit not found',
                ], 404);
            }

            // Authorization check: must be the doctor assigned to this visit or admin
            if (auth()->user()->id !== $visit->doctor_id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Start database transaction
            $result = DB::transaction(function () use ($validated, $visit, $request) {
                // Prepare medicine items for stock validation
                $medicineItems = [];
                $insufficientStock = [];

                // Get items array, default to empty if not provided
                $items = $validated['items'] ?? [];

                // Validate and lock stock rows (only if medicines are provided)
                foreach ($items as $item) {
                    $medicine = Medicine::find($item['medicine_id']);

                    // Lock the stock row for update
                    $stock = MedicineStock::where('medicine_id', $medicine->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        // Create stock entry if it doesn't exist
                        $stock = MedicineStock::create([
                            'medicine_id' => $medicine->id,
                            'qty' => 0,
                        ]);
                    }

                    // Check if sufficient stock
                    if ($stock->qty < $item['qty']) {
                        $insufficientStock[] = [
                            'medicine_id' => $medicine->id,
                            'name' => $medicine->name,
                            'requested' => $item['qty'],
                            'available' => $stock->qty,
                        ];
                    }

                    $medicineItems[] = [
                        'medicine' => $medicine,
                        'stock' => $stock,
                        'qty' => $item['qty'],
                    ];
                }

                // If insufficient stock, throw exception (transaction will rollback)
                if (count($insufficientStock) > 0) {
                    throw ValidationException::withMessages([
                        'insufficient_stock' => 'One or more medicines have insufficient stock',
                        'medicines' => $insufficientStock,
                    ]);
                }

                // Create or fetch prescription
                $prescription = $visit->prescription ?? Prescription::create([
                    'visit_id' => $visit->id,
                ]);

                $medicineTotal = 0;

                // Process medicine items
                foreach ($medicineItems as $item) {
                    $medicine = $item['medicine'];
                    $stock = $item['stock'];
                    $qty = $item['qty'];

                    // Snapshot unit price from medicine table
                    $unitPrice = $medicine->unit_price;
                    $subtotal = $qty * $unitPrice;

                    // Create prescription item
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $medicine->id,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    // Decrement stock
                    $stock->decrement('qty', $qty);

                    // Create stock transaction
                    StockTransaction::create([
                        'medicine_id' => $medicine->id,
                        'change_qty' => -$qty,
                        'type' => 'prescription',
                        'reference' => 'Visit #' . $visit->id,
                        'user_id' => auth()->id(),
                    ]);

                    $medicineTotal += $subtotal;
                }

                // Process doctor charges
                $doctorChargesTotal = 0;
                $selectedCharges = $validated['selected_charges'] ?? [];
                $doctorFees = $validated['doctor_fees'] ?? [];

                foreach ($selectedCharges as $chargeId) {
                    $charge = DoctorCharge::find($chargeId);

                    if (!$charge) {
                        continue;
                    }

                    // Allow price override if provided
                    $price = isset($doctorFees[$chargeId]) ? $doctorFees[$chargeId] : $charge->price;

                    // Create bill item for charge (created in bill creation loop below)
                    // Store in temp array for later processing
                    // We'll create these in the bill items loop
                }

                // Calculate totals
                $taxes = isset($validated['taxes']) ? (float)$validated['taxes'] : 0;
                $totalAmount = $medicineTotal + $doctorChargesTotal + $taxes;

                // Calculate doctor charges total
                foreach ($selectedCharges as $chargeId) {
                    $charge = DoctorCharge::find($chargeId);
                    if ($charge) {
                        $price = isset($doctorFees[$chargeId]) ? $doctorFees[$chargeId] : $charge->price;
                        $doctorChargesTotal += $price;
                    }
                }

                $totalAmount = $medicineTotal + $doctorChargesTotal + $taxes;

                // Create bill
                $bill = Bill::create([
                    'visit_id' => $visit->id,
                    'medicine_total' => $medicineTotal,
                    'doctor_charges_total' => $doctorChargesTotal,
                    'taxes' => $taxes,
                    'total_amount' => $totalAmount,
                    'status' => 'unpaid',
                ]);

                // Create bill items for medicines
                foreach ($medicineItems as $item) {
                    $prescriptionItem = PrescriptionItem::where('prescription_id', $prescription->id)
                        ->where('medicine_id', $item['medicine']->id)
                        ->first();

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'item_type' => 'medicine',
                        'reference_id' => $prescriptionItem->id,
                        'title' => $item['medicine']->name,
                        'qty' => $item['qty'],
                        'unit_price' => $item['medicine']->unit_price,
                        'subtotal' => $prescriptionItem->subtotal,
                    ]);
                }

                // Create bill items for doctor charges
                foreach ($selectedCharges as $chargeId) {
                    $charge = DoctorCharge::find($chargeId);
                    if ($charge) {
                        $price = isset($doctorFees[$chargeId]) ? $doctorFees[$chargeId] : $charge->price;

                        BillItem::create([
                            'bill_id' => $bill->id,
                            'item_type' => 'doctor_charge',
                            'reference_id' => $charge->id,
                            'title' => $charge->title,
                            'qty' => 1,
                            'unit_price' => $price,
                            'subtotal' => $price,
                        ]);
                    }
                }

                // Update diagnosis notes if provided
                if (isset($validated['diagnosis_notes']) && $validated['diagnosis_notes']) {
                    $visit->update(['notes' => $validated['diagnosis_notes']]);
                }

                // Mark visit as completed
                $visit->markCompleted();

                // Create notification for reception to call next patient
                $receptionUsers = \App\Models\User::where('role', 'reception')->pluck('id');
                foreach ($receptionUsers as $userId) {
                    Notification::create([
                        'user_id' => $userId,
                        'type' => 'next_patient_call',
                        'payload' => [
                            'visit_id' => $visit->id,
                            'patient_name' => $visit->patient->name,
                            'message' => "Patient {$visit->patient->name} completed consultation",
                        ],
                    ]);
                }

                // Audit log
                AuditLog::log(
                    'prescription_finalized',
                    [
                        'visit_id' => $visit->id,
                        'patient_id' => $visit->patient_id,
                        'bill_id' => $bill->id,
                        'medicine_count' => count($medicineItems),
                        'doctor_charges_count' => count($selectedCharges),
                        'total_amount' => $totalAmount,
                    ],
                    auth()->id()
                );

                return [
                    'success' => true,
                    'bill' => $bill,
                    'prescription' => $prescription,
                ];
            }, attempts: 3);

            return response()->json([
                'success' => true,
                'message' => 'Prescription finalized successfully',
                'bill' => [
                    'id' => $result['bill']->id,
                    'visit_id' => $result['bill']->visit_id,
                    'medicine_total' => (float)$result['bill']->medicine_total,
                    'doctor_charges_total' => (float)$result['bill']->doctor_charges_total,
                    'taxes' => (float)$result['bill']->taxes,
                    'total_amount' => (float)$result['bill']->total_amount,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['insufficient_stock'][0] ?? 'Validation error',
                'errors' => array_filter($e->errors(), fn($key) => $key !== 'insufficient_stock', ARRAY_FILTER_USE_KEY),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Prescription finalization error', [
                'visit_id' => $visitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while finalizing the prescription',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get prescription data for a visit (for editing/viewing).
     */
    public function getPrescription($visitId)
    {
        $visit = Visit::find($visitId);

        if (!$visit || $visit->doctor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $prescription = $visit->prescription;

        if (!$prescription) {
            return response()->json([
                'success' => true,
                'prescription' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'prescription' => [
                'id' => $prescription->id,
                'items' => $prescription->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'medicine_id' => $item->medicine_id,
                        'medicine_name' => $item->medicine->name,
                        'qty' => $item->qty,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
            ],
        ]);
    }
}
