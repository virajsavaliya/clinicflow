<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class MedicineApiController extends Controller
{
    /**
     * Create a new medicine.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:medicines,name',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        try {
            $medicine = Medicine::create([
                'name' => $request->name,
                'unit_price' => $request->unit_price,
                'reorder_level' => $request->reorder_level ?? 10,
            ]);

            // Create initial stock entry with 0 quantity
            MedicineStock::create([
                'medicine_id' => $medicine->id,
                'qty' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine created successfully',
                'medicine' => $medicine,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a medicine.
     */
    public function update(Request $request, $medicineId)
    {
        $medicine = Medicine::find($medicineId);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:medicines,name,' . $medicineId,
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        try {
            $medicine->update($request->only([
                'name', 'unit_price', 'reorder_level'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Medicine updated successfully',
                'medicine' => $medicine,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a medicine.
     */
    public function delete($medicineId)
    {
        $medicine = Medicine::find($medicineId);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found',
            ], 404);
        }

        try {
            $medicine->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medicine deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add stock to a medicine.
     */
    public function addStock(Request $request, $medicineId)
    {
        $medicine = Medicine::find($medicineId);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found',
            ], 404);
        }

        $request->validate([
            'quantity'        => 'required|integer|min:1',
            'adjustment_type' => 'nullable|in:add,remove',
            'notes'           => 'nullable|string',
        ]);

        try {
            $qty         = (int) $request->quantity;
            $adjustType  = $request->input('adjustment_type', 'add');

            // If removing, negate the quantity
            if ($adjustType === 'remove') {
                $qty = -$qty;
            }

            // Update the medicine_stock record
            $stock = MedicineStock::where('medicine_id', $medicine->id)->first();

            if (!$stock) {
                $stock = MedicineStock::create([
                    'medicine_id' => $medicine->id,
                    'qty'         => 0,
                ]);
            }

            $newQty = $stock->qty + $qty;

            if ($newQty < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock cannot go below zero. Current stock: ' . $stock->qty,
                ], 422);
            }

            $stock->update(['qty' => $newQty]);

            // Log the stock transaction with correct column names
            StockTransaction::create([
                'medicine_id' => $medicine->id,
                'type'        => $adjustType === 'remove' ? 'adjustment' : 'purchase',
                'change_qty'  => $qty,
                'reference'   => $request->input('notes', 'Manual stock adjustment'),
                'user_id'     => auth()->id(),
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Stock updated successfully',
                'new_stock' => $newQty,
                'medicine'  => $medicine->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Search medicines by name or barcode.
     *
     * Query params:
     * - q: search query (name or barcode)
     * - limit: max results (default 10)
     *
     * Response:
     * {
     *   "success": true,
     *   "medicines": [
     *     {
     *       "id": 1,
     *       "name": "Aspirin",
     *       "barcode": "1234567890",
     *       "unit_price": 10.50,
     *       "current_stock": 50
     *     }
     *   ]
     * }
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'medicines' => [],
            ]);
        }

        $medicines = Medicine::where('name', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->with('stock')
            ->limit($limit)
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'barcode' => $medicine->barcode,
                    'unit_price' => (float)$medicine->unit_price,
                    'current_stock' => $medicine->currentStock(),
                    'reorder_level' => $medicine->reorder_level,
                ];
            });

        return response()->json([
            'success' => true,
            'medicines' => $medicines,
        ]);
    }

    /**
     * Get stock information for a medicine.
     *
     * Response:
     * {
     *   "success": true,
     *   "medicine": {
     *     "id": 1,
     *     "name": "Aspirin",
     *     "unit_price": 10.50,
     *     "current_stock": 50,
     *     "reorder_level": 20,
     *     "is_low": false
     *   }
     * }
     */
    public function getStock($medicineId)
    {
        $medicine = Medicine::with('stock')->find($medicineId);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'medicine' => [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'unit_price' => (float)$medicine->unit_price,
                'current_stock' => $medicine->currentStock(),
                'reorder_level' => $medicine->reorder_level,
                'is_low' => $medicine->isLowOnStock(),
            ],
        ]);
    }

    /**
     * Get all medicines with current stock.
     */
    public function getAll(Request $request)
    {
        $limit = $request->input('limit', 50);

        $medicines = Medicine::with('stock')
            ->limit($limit)
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'barcode' => $medicine->barcode,
                    'unit_price' => (float)$medicine->unit_price,
                    'current_stock' => $medicine->currentStock(),
                    'reorder_level' => $medicine->reorder_level,
                    'is_low' => $medicine->isLowOnStock(),
                ];
            });

        return response()->json([
            'success' => true,
            'medicines' => $medicines,
        ]);
    }

    /**
     * Adjust medicine stock (add or remove).
     */
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|integer|exists:medicines,id',
            'qty' => 'required|integer',
            'type' => 'required|in:add,remove,purchase,adjustment,correction',
        ]);

        try {
            $medicine = Medicine::find($validated['medicine_id']);

            // Convert add/remove to adjustment
            $adjustType = $validated['type'];
            $qty = $validated['qty'];
            
            // If removing, make the quantity negative
            if ($adjustType === 'remove') {
                $qty = -$qty;
                $adjustType = 'adjustment';
            } elseif ($adjustType === 'add') {
                $adjustType = 'adjustment';
            }

            // Lock and update stock
            $stock = MedicineStock::where('medicine_id', $medicine->id)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = MedicineStock::create([
                    'medicine_id' => $medicine->id,
                    'qty' => 0,
                ]);
            }

            $newQty = $stock->qty + $qty;

            // Ensure stock doesn't go negative
            if ($newQty < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock cannot go below zero',
                ], 422);
            }

            $stock->update(['qty' => $newQty]);

            // Log the transaction
            StockTransaction::create([
                'medicine_id' => $medicine->id,
                'type' => $adjustType,
                'change_qty' => $qty,
                'reference' => $request->input('notes', 'Adjusted via API'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'medicine' => $medicine,
                'new_stock' => $newQty,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adjusting stock: ' . $e->getMessage(),
            ], 500);
        }
    }
}
