<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Patient;
use App\Models\StockTransaction;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    /**
     * Display the reception dashboard.
     */
    public function dashboard()
    {
        if (!request()->has('build')) {
            return redirect()->route('reception.dashboard', [
                'build' => now()->timestamp,
            ]);
        }

        $currentUser = auth()->user();
        
        // Get the doctor this receptionist works for
        $doctorId = $currentUser->created_by_doctor_id;
        
        // Build query for today's visits
        $visitsQuery = Visit::whereDate('created_at', today())
            ->with('patient', 'doctor')
            ->orderBy('queue_number');
        
        // If receptionist is linked to a doctor, only show that doctor's visits
        if ($doctorId) {
            $visitsQuery->where('doctor_id', $doctorId);
        }
        
        $todayVisits = $visitsQuery->get();

        $lowStockMedicines = Medicine::whereHas('stock', function ($q) {
            $q->whereColumn('qty', '<=', 'medicines.reorder_level');
        })->with('stock')->get();
        
        // Get today's appointments
        $todayAppointments = collect([]);
        if ($doctorId) {
            $todayAppointments = \App\Models\Appointment::with('patient')
                ->where('doctor_id', $doctorId)
                ->whereDate('appointment_date', today())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->orderBy('appointment_time')
                ->get();
        }

        return view('reception.dashboard', [
            'todayVisits' => $todayVisits,
            'lowStockMedicines' => $lowStockMedicines,
            'todayAppointments' => $todayAppointments,
        ]);
    }

    /**
     * Enqueue a patient for a visit.
     *
     * Request:
     * {
     *   "patient_id": 1 (optional, if existing patient),
     *   "name": "John Doe",
     *   "phone": "1234567890",
     *   "age": 30,
     *   "gender": "male",
     *   "address": "123 Main St"
     * }
     */
    public function enqueuePatient(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|integer|exists:patients,id',
            'name' => 'required_without:patient_id|string|max:255|min:2',
            'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'action' => 'nullable|in:register,queue', // New field for action type
        ], [
            'phone.regex' => 'Phone number must be exactly 10 digits',
            'name.min' => 'Patient name must be at least 2 characters',
            'name.required_without' => 'Patient name is required when adding a new patient',
            'birthdate.before_or_equal' => 'Birthdate cannot be in the future',
            'age.max' => 'Age cannot exceed 150 years',
        ]);

        try {
            $patient = null;
            $action = $validated['action'] ?? 'queue'; // Default to queue for backward compatibility

            if (isset($validated['patient_id']) && $validated['patient_id']) {
                $patient = Patient::find($validated['patient_id']);
            } else {
                // Calculate age from birthdate if provided
                if (!empty($validated['birthdate'])) {
                    $validated['age'] = \Carbon\Carbon::parse($validated['birthdate'])->age;
                }
                
                // Create or find patient by name
                $patient = Patient::firstOrCreate(
                    ['name' => $validated['name']],
                    [
                        'phone' => $validated['phone'] ?? null,
                        'birthdate' => $validated['birthdate'] ?? null,
                        'age' => $validated['age'] ?? null,
                        'gender' => $validated['gender'] ?? null,
                        'address' => $validated['address'] ?? null,
                    ]
                );

                // Update patient if new data provided
                if ($patient->wasRecentlyCreated === false) {
                    $updateData = [
                        'phone' => $validated['phone'] ?? $patient->phone,
                        'gender' => $validated['gender'] ?? $patient->gender,
                        'address' => $validated['address'] ?? $patient->address,
                    ];
                    
                    // Update birthdate and recalculate age if birthdate is provided
                    if (!empty($validated['birthdate'])) {
                        $updateData['birthdate'] = $validated['birthdate'];
                        $updateData['age'] = \Carbon\Carbon::parse($validated['birthdate'])->age;
                    } elseif (!empty($validated['age'])) {
                        $updateData['age'] = $validated['age'];
                    }
                    
                    $patient->update($updateData);
                }
            }

            // If action is "register only", just return patient info without creating visit
            if ($action === 'register') {
                AuditLog::log('patient_registered', [
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->name,
                ], auth()->id());

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Patient registered successfully',
                        'patient' => [
                            'id' => $patient->id,
                            'name' => $patient->name,
                            'phone' => $patient->phone,
                            'age' => $patient->age,
                            'gender' => $patient->gender,
                        ],
                    ]);
                }

                return redirect()->route('reception.patients')->with('success', 'Patient registered successfully!');
            }

            // If action is "queue" (default), create visit and add to queue
            // Get next queue number for today
            $maxQueueNumber = Visit::whereDate('created_at', today())
                ->max('queue_number') ?? 0;

            // Get the doctor ID - reception staff works for the doctor who created them
            $currentUser = auth()->user();
            $doctor = null;
            
            // If reception user was created by a doctor, use that doctor
            if ($currentUser->created_by_doctor_id) {
                $doctor = \App\Models\User::find($currentUser->created_by_doctor_id);
            }
            
            // Fallback: get the first available doctor in the system
            if (!$doctor) {
                $doctor = \App\Models\User::where('role', 'doctor')->first();
            }
            
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No doctor available in the system. Please contact admin.',
                ], 400);
            }

            // Create visit assigned to the correct doctor
            $visit = Visit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'queue_number' => $maxQueueNumber + 1,
                'status' => 'waiting',
            ]);

            AuditLog::log('patient_enqueued', [
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'queue_number' => $visit->queue_number,
            ], auth()->id());

            // Check if request expects JSON (AJAX) or wants a redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Patient enqueued successfully',
                    'visit' => [
                        'id' => $visit->id,
                        'queue_number' => $visit->queue_number,
                        'patient_name' => $patient->name,
                        'status' => $visit->status,
                    ],
                ]);
            }
            
            // Regular form submission - redirect to dashboard
            return redirect()->route('reception.dashboard')
                ->with('success', 'Patient "' . $patient->name . '" added to queue successfully! Queue #' . $visit->queue_number);
                
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to enqueue patient: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to enqueue patient: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Add a new medicine to inventory.
     *
     * Request:
     * {
     *   "name": "Aspirin",
     *   "barcode": "1234567890",
     *   "unit_price": 10.50,
     *   "reorder_level": 20
     * }
     */
    public function addMedicine(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:medicines,barcode',
            'unit_price' => 'required|numeric|min:0.01',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        try {
            $medicine = Medicine::create([
                'name' => $validated['name'],
                'barcode' => $validated['barcode'] ?? null,
                'unit_price' => $validated['unit_price'],
                'reorder_level' => $validated['reorder_level'] ?? 0,
            ]);

            // Create stock entry
            MedicineStock::create([
                'medicine_id' => $medicine->id,
                'qty' => 0,
            ]);

            AuditLog::log('medicine_added', [
                'medicine_id' => $medicine->id,
                'name' => $medicine->name,
            ], auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Medicine added successfully',
                'medicine' => [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'unit_price' => (float)$medicine->unit_price,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust medicine stock (add or correct inventory).
     *
     * Request:
     * {
     *   "medicine_id": 1,
     *   "qty": 50,
     *   "type": "purchase"  // or "adjustment" or "correction"
     * }
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

            $stock->increment('qty', $qty);

            // Create stock transaction
            StockTransaction::create([
                'medicine_id' => $medicine->id,
                'change_qty' => $qty,
                'type' => $adjustType,
                'reference' => "Manual {$adjustType}",
                'user_id' => auth()->id(),
            ]);

            AuditLog::log('stock_adjusted', [
                'medicine_id' => $medicine->id,
                'change_qty' => $qty,
                'type' => $adjustType,
                'new_qty' => $newQty,
            ], auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'medicine' => [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'current_stock' => $stock->qty,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread notifications for reception.
     */
    public function notifications()
    {
        $notifications = auth()->user()
            ->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reception.notifications', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Search for existing patients by name (for autocomplete).
     *
     * Query params:
     * - q: search query (patient name)
     * - limit: number of results (default: 10)
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'age', 'gender', 'phone')
            ->limit($limit)
            ->get();

        return response()->json($patients);
    }

    /**
     * Get patient visit history (past visits, diagnoses, medicines).
     */
    public function getPatientHistory($patientId)
    {
        $patient = Patient::find($patientId);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        $visits = Visit::where('patient_id', $patientId)
            ->where('status', 'completed')
            ->with(['prescription.items.medicine', 'doctor'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'date' => $visit->completed_at?->format('M d, Y') ?? $visit->created_at->format('M d, Y'),
                    'doctor' => $visit->doctor->name ?? 'Unknown',
                    'diagnosis' => $visit->notes,
                    'medicines' => $visit->prescription?->items->map(fn($item) => [
                        'name' => $item->medicine->name,
                        'dosage' => $item->dosage,
                        'duration' => $item->duration,
                    ])->values() ?? [],
                ];
            });

        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'age' => $patient->age,
                'gender' => $patient->gender,
                'phone' => $patient->phone,
            ],
            'history' => $visits,
        ]);
    }

    /**
     * Display all patients with search capability.
     */
    public function patients()
    {
        $search = request()->get('search', '');

        $patients = Patient::query();

        if ($search) {
            $patients->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $patients = $patients->with('visits')->latest('created_at')->paginate(15);
        $total = Patient::count();

        return view('reception.patients', [
            'patients' => $patients,
            'total' => $total,
        ]);
    }

    /**
     * Display pharmacy/medicine inventory.
     */
    public function pharmacy()
    {
        $search = request()->get('search', '');

        $medicines = Medicine::query();

        if ($search) {
            $medicines->where('name', 'LIKE', "%{$search}%");
        }

        $medicines = $medicines->with('stock')->latest('created_at')->get();
        $lowStockMedicines = Medicine::whereHas('stock', function ($q) {
            $q->whereColumn('qty', '<=', 'medicines.reorder_level');
        })->with('stock')->get();

        return view('reception.pharmacy', [
            'medicines' => $medicines,
            'lowStockMedicines' => $lowStockMedicines,
        ]);
    }

    /**
     * API: Update patient information
     */
    public function updatePatient(Request $request, $id)
    {
        try {
            $patient = Patient::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'age' => 'nullable|integer|min:0|max:150',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string|max:500',
            ]);

            $patient->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete patient
     */
    public function deletePatient($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $name = $patient->name;
            
            // Delete related records (appointments, visits, prescriptions)
            // But keep billing/revenue records for financial auditing
            $patient->visits()->delete();
            
            // Delete appointments if relationship exists
            if (method_exists($patient, 'appointments')) {
                $patient->appointments()->delete();
            }
            
            // Delete prescriptions if relationship exists
            if (method_exists($patient, 'prescriptions')) {
                $patient->prescriptions()->delete();
            }
            
            // Delete the patient record
            $patient->delete();

            return response()->json([
                'success' => true,
                'message' => "Patient '{$name}' and all related records deleted successfully"
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search patients by phone number
     */
    public function searchByPhone(Request $request)
    {
        try {
            $phone = $request->get('phone');
            
            if (!$phone || strlen($phone) < 3) {
                return response()->json([
                    'success' => true,
                    'patients' => []
                ]);
            }
            
            // Search for patients with matching phone numbers
            $patients = Patient::where('phone', 'LIKE', "%{$phone}%")
                ->select('id', 'name', 'phone', 'age', 'gender')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'patients' => $patients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Create new medicine
     */
    public function createMedicine(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:medicines,name',
                'unit_price' => 'required|numeric|min:0',
                'reorder_level' => 'nullable|integer|min:0',
            ]);

            $medicine = Medicine::create($validated);
            
            // Create stock record
            MedicineStock::create([
                'medicine_id' => $medicine->id,
                'qty' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine created successfully',
                'medicine' => $medicine->load('stock')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update medicine
     */
    public function updateMedicine(Request $request, $id)
    {
        try {
            $medicine = Medicine::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:medicines,name,' . $id,
                'unit_price' => 'required|numeric|min:0',
                'reorder_level' => 'nullable|integer|min:0',
            ]);

            $medicine->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Medicine updated successfully',
                'medicine' => $medicine->load('stock')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete medicine
     */
    public function deleteMedicine($id)
    {
        try {
            $medicine = Medicine::findOrFail($id);
            $name = $medicine->name;
            
            // Delete related records
            $medicine->stock()->delete();
            $medicine->delete();

            return response()->json([
                'success' => true,
                'message' => "Medicine '{$name}' deleted successfully"
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display notification settings page
     */
    public function notificationSettings()
    {
        // TODO: Load saved settings from database
        return view('reception.notification-settings');
    }

    /**
     * Save notification settings
     */
    public function saveNotificationSettings(Request $request)
    {
        // TODO: Save to database
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Notification settings saved successfully!'
        ]);
    }
}

