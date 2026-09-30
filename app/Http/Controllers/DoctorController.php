<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorCharge;
use App\Models\DoctorProfile;
use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * Display the doctor's dashboard with their queue.
     */
    public function dashboard()
    {
        $doctor = auth()->user();
        $doctorProfile = $doctor->doctorProfile;

        $todayQueue = Visit::where('doctor_id', $doctor->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'in_consult'])
            ->with('patient')
            ->orderBy('queue_number')
            ->get()
            ->filter(function($visit) {
                return $visit->patient !== null; // Filter out visits with deleted patients
            })
            ->values(); // Reindex the collection after filtering

        $completedCount = Visit::where('doctor_id', $doctor->id)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
        
        // Today's appointments count
        $todayAppointments = 0;
        $upcomingAppointments = collect();
        if (class_exists(\App\Models\Appointment::class)) {
            try {
                $todayAppointments = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', today())
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->count();
                
                $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', today())
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->with('patient')
                    ->orderBy('appointment_time')
                    ->limit(5)
                    ->get()
                    ->filter(function($apt) {
                        return $apt->patient !== null; // Filter out appointments with deleted patients
                    });
            } catch (\Exception $e) {
                // Table may not exist yet
            }
        }
        
        // Inventory alerts
        $inventoryAlerts = [];
        $alertCount = 0;
        try {
            // Low stock items
            $lowStock = Medicine::whereHas('stock', function($q) {
                $q->where('qty', '<', 10);
            })->with('stock')->limit(5)->get();
            
            foreach ($lowStock as $med) {
                $qty = $med->stock?->qty ?? 0;
                $inventoryAlerts[] = [
                    'name' => $med->name,
                    'stock' => $qty,
                    'status' => $qty < 5 ? 'critical' : 'low',
                ];
            }
            
            // Expiring soon
            $expiring = MedicineStock::with('medicine')
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [today(), today()->addDays(30)])
                ->where('qty', '>', 0)
                ->limit(3)
                ->get();
            
            foreach ($expiring as $stock) {
                $inventoryAlerts[] = [
                    'name' => $stock->medicine->name ?? 'Unknown',
                    'stock' => $stock->qty,
                    'status' => 'expiring',
                ];
            }
            
            $alertCount = count($inventoryAlerts);
        } catch (\Exception $e) {
            // Ignore if columns don't exist
        }

        return view('doctor.dashboard', [
            'doctorProfile' => $doctorProfile,
            'todayQueue' => $todayQueue,
            'completedCount' => $completedCount,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'inventoryAlerts' => $inventoryAlerts,
            'alertCount' => $alertCount,
        ]);
    }

    /**
     * Open a visit and display patient details and prescription form.
     * Doctors can view visits assigned to them OR visits from the same clinic.
     */
    public function openVisit($visitId)
    {
        $visit = Visit::with('patient', 'prescription.items.medicine', 'doctor.doctorProfile')
            ->find($visitId);

        if (!$visit) {
            abort(404, 'Visit not found');
        }

        // Check authorization: same doctor OR same clinic
        $currentDoctor = auth()->user();
        $currentDoctorProfile = $currentDoctor->doctorProfile;
        
        $isOwnVisit = $visit->doctor_id === $currentDoctor->id;
        $isSameClinic = false;
        
        // Check if both doctors belong to the same clinic
        if (!$isOwnVisit && $currentDoctorProfile && $visit->doctor && $visit->doctor->doctorProfile) {
            $visitDoctorClinic = $visit->doctor->doctorProfile->clinic_name;
            $currentDoctorClinic = $currentDoctorProfile->clinic_name;
            
            // Same clinic if both have a clinic name and they match
            if ($visitDoctorClinic && $currentDoctorClinic && $visitDoctorClinic === $currentDoctorClinic) {
                $isSameClinic = true;
            }
        }
        
        if (!$isOwnVisit && !$isSameClinic) {
            abort(403, 'Unauthorized - You can only view your own visits or visits from your clinic');
        }

        // Mark as in consultation if not already
        if ($visit->isWaiting()) {
            $visit->markInConsult();
        }

        $doctorProfile = auth()->user()->doctorProfile;
        $activeCharges = $doctorProfile->activeCharges()->get();

        return view('doctor.visit', [
            'visit' => $visit,
            'doctorProfile' => $doctorProfile,
            'doctorCharges' => $activeCharges,
        ]);
    }

    /**
     * Add or update diagnosis notes for a visit.
     *
     * Request:
     * {
     *   "notes": "Patient has mild fever and cough"
     * }
     */
    public function addDiagnosis(Request $request, $visitId)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:2000',
        ]);

        $visit = Visit::find($visitId);

        if (!$visit || $visit->doctor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $visit->update(['notes' => $validated['notes']]);

        AuditLog::log('diagnosis_added', [
            'visit_id' => $visit->id,
            'length' => strlen($validated['notes']),
        ], auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis notes saved',
        ]);
    }

    /**
     * Update clinic settings for the doctor.
     *
     * Request (multipart/form-data):
     * {
     *   "clinic_name": "My Clinic",
     *   "clinic_logo_cropped": "<base64 data URL from crop>",
     *   "default_doctor_fee": 100.00
     * }
     */
    public function manageClinicSettings(Request $request)
    {
        $validated = $request->validate([
            'clinic_name'         => 'nullable|string|max:255',
            'clinic_logo_cropped' => 'nullable|string',
            'default_doctor_fee'  => 'nullable|numeric|min:0',
        ]);

        try {
            $doctorProfile = auth()->user()->doctorProfile;

            if (!$doctorProfile) {
                $doctorProfile = DoctorProfile::create([
                    'user_id' => auth()->id(),
                ]);
            }

            $updateData = [];

            if (isset($validated['clinic_name'])) {
                $updateData['clinic_name'] = $validated['clinic_name'];
            }

            if (isset($validated['default_doctor_fee'])) {
                $updateData['default_doctor_fee'] = $validated['default_doctor_fee'];
            }

            // Handle cropped logo (base64 data URL)
            if (!empty($validated['clinic_logo_cropped'])) {
                $dataUrl = $validated['clinic_logo_cropped'];

                // Validate it is a real image data URL
                if (!preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/i', $dataUrl, $matches)) {
                    return redirect()->route('doctor.clinic.settings')->with('error', 'Invalid image data.');
                }

                // Decode base64
                $rawBase64   = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
                $imageData   = base64_decode($rawBase64);

                if (!$imageData) {
                    return redirect()->route('doctor.clinic.settings')->with('error', 'Could not decode image.');
                }

                // ── Compress & resize with GD ──────────────────────────────
                $compressedPng = $this->compressLogoWithGd($imageData, 512, 512, 85);

                if (!$compressedPng) {
                    // Fall back to raw data if GD fails for any reason
                    $compressedPng = $imageData;
                }

                // Enforce 2 MB hard cap after compression
                if (strlen($compressedPng) > 2 * 1024 * 1024) {
                    return redirect()->route('doctor.clinic.settings')->with('error', 'Image is still too large after compression (max 2MB). Please use a smaller image.');
                }
                // ──────────────────────────────────────────────────────────

                // Delete old logo if exists
                if ($doctorProfile->clinic_logo && Storage::disk('public')->exists($doctorProfile->clinic_logo)) {
                    Storage::disk('public')->delete($doctorProfile->clinic_logo);
                }

                // Save compressed PNG to storage/app/public/clinic_logos/
                $filename = 'clinic_logos/' . \Illuminate\Support\Str::random(40) . '.png';
                Storage::disk('public')->put($filename, $compressedPng);

                $updateData['clinic_logo'] = $filename;
            }

            $doctorProfile->update($updateData);

            AuditLog::log('clinic_settings_updated', [
                'doctor_id' => auth()->id(),
                'has_logo'  => isset($updateData['clinic_logo']),
            ], auth()->id());

            return redirect()->route('doctor.clinic.settings')->with('success', 'Clinic settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('doctor.clinic.settings')->with('error', 'Failed to update clinic settings: ' . $e->getMessage());
        }
    }

    /**
     * Compress and resize an image using GD.
     *
     * @param  string $imageData  Raw binary image data
     * @param  int    $maxWidth   Maximum output width in pixels
     * @param  int    $maxHeight  Maximum output height in pixels
     * @param  int    $quality    JPEG quality (1-100); PNG uses inverse (0-9 compression)
     * @return string|null        Compressed PNG binary, or null on failure
     */
    private function compressLogoWithGd(string $imageData, int $maxWidth = 512, int $maxHeight = 512, int $quality = 85): ?string
    {
        // Create GD image resource from raw data
        $src = @imagecreatefromstring($imageData);
        if (!$src) {
            return null;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Calculate new dimensions preserving aspect ratio
        $ratio   = min($maxWidth / $origW, $maxHeight / $origH, 1.0); // never upscale
        $newW    = (int) round($origW * $ratio);
        $newH    = (int) round($origH * $ratio);

        // Create a true-colour canvas with alpha support
        $canvas = imagecreatetruecolor($newW, $newH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        // Fill with transparent background (preserves PNG transparency)
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        // High-quality resize
        imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        imagedestroy($src);

        // Output to buffer as PNG
        // PNG compression: 0 (no compression) – 9 (max). Map quality 85 → compression ~2
        $pngCompression = (int) round((100 - $quality) / 11); // 85 quality → ~1
        $pngCompression = max(0, min(9, $pngCompression));

        ob_start();
        imagepng($canvas, null, $pngCompression);
        $output = ob_get_clean();

        imagedestroy($canvas);

        return $output ?: null;
    }

    /**
     * Get clinic settings for the doctor.
     */
    public function getClinicSettings()
    {
        $doctorProfile = auth()->user()->doctorProfile;

        return view('doctor.clinic-settings', [
            'profile' => $doctorProfile
        ]);
    }

    /**
     * Delete clinic logo.
     */
    public function deleteClinicLogo()
    {
        try {
            $doctorProfile = auth()->user()->doctorProfile;

            if (!$doctorProfile || !$doctorProfile->clinic_logo) {
                return redirect()->route('doctor.clinic.settings')->with('error', 'No logo to delete');
            }

            // Delete logo file from storage
            if (Storage::exists($doctorProfile->clinic_logo)) {
                Storage::delete($doctorProfile->clinic_logo);
            }

            // Update profile to remove logo reference
            $doctorProfile->update(['clinic_logo' => null]);

            AuditLog::log('clinic_logo_deleted', [
                'doctor_id' => auth()->id(),
            ], auth()->id());

            return redirect()->route('doctor.clinic.settings')->with('success', 'Clinic logo deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('doctor.clinic.settings')->with('error', 'Failed to delete logo: ' . $e->getMessage());
        }
    }

    /**
     * Get doctor charges (CRUD).
     */
    public function getDoctorCharges()
    {
        $doctorProfile = auth()->user()->doctorProfile;

        $charges = [];
        if ($doctorProfile) {
            $charges = $doctorProfile->charges()
                ->get()
                ->map(function ($charge) {
                    return [
                        'id' => $charge->id,
                        'title' => $charge->title,
                        'price' => (float)$charge->price,
                        'is_active' => $charge->is_active,
                    ];
                })->toArray();
        }

        return view('doctor.charges', [
            'charges' => $charges
        ]);
    }

    /**
     * Create a new doctor charge.
     *
     * Request:
     * {
     *   "title": "Checkup",
     *   "price": 80.00
     * }
     */
    public function createDoctorCharge(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
        ]);

        try {
            $doctorProfile = auth()->user()->doctorProfile;

            if (!$doctorProfile) {
                $doctorProfile = DoctorProfile::create([
                    'user_id' => auth()->id(),
                ]);
            }

            $charge = DoctorCharge::create([
                'doctor_profile_id' => $doctorProfile->id,
                'title' => $validated['title'],
                'price' => $validated['price'],
                'is_active' => true,
            ]);

            AuditLog::log('doctor_charge_created', [
                'doctor_id' => auth()->id(),
                'charge_id' => $charge->id,
                'title' => $charge->title,
                'price' => $charge->price,
            ], auth()->id());

            return redirect()->route('doctor.charges.list')->with('success', 'Service charge created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('doctor.charges.list')->with('error', 'Failed to create charge: ' . $e->getMessage());
        }
    }

    /**
     * Update a doctor charge.
     *
     * Request:
     * {
     *   "title": "Checkup",
     *   "price": 100.00,
     *   "is_active": true
     * }
     */
    public function updateDoctorCharge(Request $request, $chargeId)
    {
        $charge = DoctorCharge::find($chargeId);

        if (!$charge || $charge->doctorProfile->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0.01',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $updateData = [];

            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
            }
            if (isset($validated['price'])) {
                $updateData['price'] = $validated['price'];
            }
            // Handle checkbox: if not set, it means unchecked = false
            $updateData['is_active'] = $request->has('is_active');

            $charge->update($updateData);

            AuditLog::log('doctor_charge_updated', [
                'doctor_id' => auth()->id(),
                'charge_id' => $charge->id,
            ], auth()->id());

            return redirect()->route('doctor.charges.list')->with('success', 'Service charge updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('doctor.charges.list')->with('error', 'Failed to update charge: ' . $e->getMessage());
        }
    }

    /**
     * Delete a doctor charge.
     */
    public function deleteDoctorCharge($chargeId)
    {
        $charge = DoctorCharge::find($chargeId);

        if (!$charge || $charge->doctorProfile->user_id !== auth()->id()) {
            return redirect()->route('doctor.charges.list')->with('error', 'Unauthorized action');
        }

        try {
            $charge->delete();

            AuditLog::log('doctor_charge_deleted', [
                'doctor_id' => auth()->id(),
                'charge_id' => $charge->id,
            ], auth()->id());

            return redirect()->route('doctor.charges.list')->with('success', 'Service charge deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('doctor.charges.list')->with('error', 'Failed to delete charge: ' . $e->getMessage());
        }
    }

    /**
     * Complete a visit (alternative endpoint).
     */
    public function completeVisit($visitId)
    {
        $visit = Visit::find($visitId);

        if (!$visit || $visit->doctor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$visit->isCompleted()) {
            $visit->markCompleted();
        }

        return response()->json([
            'success' => true,
            'message' => 'Visit marked as completed',
        ]);
    }

    /**
     * Display list of reception users (for doctors to manage).
     * Only shows receptionists created by the current doctor.
     */
    public function receptions()
    {
        $receptions = User::where('role', 'reception')
            ->where('created_by_doctor_id', auth()->id()) // Only show receptionists created by this doctor
            ->latest()
            ->paginate(15);

        return view('doctor.receptions', compact('receptions'));
    }

    /**
     * Show form to create a new reception user.
     */
    public function createReception()
    {
        return view('doctor.create-reception');
    }

    /**
     * Store a new reception user (created by doctor).
     */
    public function storeReception(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create reception user linked to this doctor
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'reception',
            'created_by_doctor_id' => auth()->id(), // Link to the doctor who created this receptionist
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('doctor.receptions')
            ->with('success', 'Reception user created successfully!');
    }

    /**
     * Show form to edit a reception user.
     * Only allows editing receptionists created by this doctor.
     */
    public function editReception($id)
    {
        $reception = User::where('role', 'reception')
            ->where('created_by_doctor_id', auth()->id())
            ->findOrFail($id);
        return view('doctor.edit-reception', compact('reception'));
    }

    /**
     * Update a reception user.
     * Only allows updating receptionists created by this doctor.
     */
    public function updateReception(Request $request, $id)
    {
        $reception = User::where('role', 'reception')
            ->where('created_by_doctor_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $reception->name = $request->name;
        $reception->email = $request->email;

        if ($request->filled('password')) {
            $reception->password = Hash::make($request->password);
        }

        $reception->save();

        return redirect()
            ->route('doctor.receptions')
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Delete a reception user.
     * Only allows deleting receptionists created by this doctor.
     */
    public function deleteReception($id)
    {
        $reception = User::where('role', 'reception')
            ->where('created_by_doctor_id', auth()->id())
            ->findOrFail($id);
        $reception->delete();

        return redirect()
            ->route('doctor.receptions')
            ->with('success', 'Staff member deleted successfully!');
    }

    /**
     * Search patients by name (autocomplete).
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('query', '');

        if (strlen($query) < 2) {
            return response()->json(['patients' => []]);
        }

        $patients = \App\Models\Patient::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json(['patients' => $patients]);
    }

    /**
     * Display list of medicines in inventory.
     */
    public function medicines()
    {
        $medicines = Medicine::with('stock')->orderBy('name')->get();

        return view('doctor.medicines', compact('medicines'));
    }

    /**
     * Display patient history search page.
     */
    public function patientHistory()
    {
        $search = request()->get('search', '');
        $patients = collect();

        if ($search) {
            $patients = Patient::where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->with(['visits' => function ($query) {
                    $query->with(['prescription.items.medicine', 'bill'])
                        ->orderBy('created_at', 'desc');
                }])
                ->orderBy('name')
                ->limit(50)
                ->get();
        }

        return view('doctor.patients', [
            'patients' => $patients,
            'search' => $search,
        ]);
    }
}