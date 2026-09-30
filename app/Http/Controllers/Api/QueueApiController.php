<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class QueueApiController extends Controller
{
    /**
     * Get the current queue for a doctor or reception.
     *
     * Query params:
     * - doctor_id: (optional) Get queue for specific doctor
     *
     * Response:
     * {
     *   "success": true,
     *   "queue": [
     *     {
     *       "id": 1,
     *       "queue_number": 1,
     *       "patient_name": "John Doe",
     *       "status": "waiting",
     *       "enqueued_at": "2025-01-15 10:30:00"
     *     }
     *   ]
     * }
     */
    public function getQueue(Request $request)
    {
        $user = auth()->user();
        
        $query = Visit::whereDate('created_at', today())
            ->with('patient', 'doctor')
            ->whereIn('status', ['waiting', 'in_consult']);

        // If doctor_id provided, filter by that doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }
        // If user is a doctor, show only their queue
        elseif ($user && $user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }
        // If user is a receptionist linked to a doctor, show that doctor's queue
        elseif ($user && $user->isReception() && $user->created_by_doctor_id) {
            $query->where('doctor_id', $user->created_by_doctor_id);
        }

        $queue = $query->orderBy('queue_number')
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'queue_number' => $visit->queue_number,
                    'patient_name' => $visit->patient->name,
                    'patient_phone' => $visit->patient->phone,
                    'patient_age' => $visit->patient->age,
                    'patient_gender' => $visit->patient->gender,
                    'status' => $visit->status,
                    'doctor_name' => $visit->doctor?->name ?? 'Unassigned',
                    'enqueued_at' => $visit->created_at,
                    'started_at' => $visit->started_at,
                ];
            });

        return response()->json([
            'success' => true,
            'queue' => $queue,
        ]);
    }

    /**
     * Get a single visit details (for AJAX updates).
     */
    public function getVisit($visitId)
    {
        $visit = Visit::with('patient', 'doctor', 'prescription.items.medicine', 'bill.items')
            ->find($visitId);

        if (!$visit) {
            return response()->json(['success' => false, 'message' => 'Visit not found'], 404);
        }

        return response()->json([
            'success' => true,
            'visit' => [
                'id' => $visit->id,
                'queue_number' => $visit->queue_number,
                'patient' => [
                    'id' => $visit->patient->id,
                    'name' => $visit->patient->name,
                    'phone' => $visit->patient->phone,
                    'age' => $visit->patient->age,
                    'gender' => $visit->patient->gender,
                ],
                'doctor' => $visit->doctor?->name,
                'status' => $visit->status,
                'notes' => $visit->notes,
                'started_at' => $visit->started_at,
                'completed_at' => $visit->completed_at,
            ],
        ]);
    }
    
    /**
     * Remove a patient from the queue (soft delete visit).
     */
    public function removeFromQueue($visitId)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;
        
        $visit = Visit::where('id', $visitId)
            ->where('doctor_id', $doctorId)
            ->whereIn('status', ['waiting', 'in_consult'])
            ->first();
        
        if (!$visit) {
            return response()->json([
                'success' => false,
                'message' => 'Visit not found or cannot be removed',
            ], 404);
        }
        
        // Use soft delete if available, otherwise mark as cancelled
        $patientName = $visit->patient->name ?? 'Patient';
        
        if (method_exists($visit, 'trashed')) {
            $visit->delete(); // Soft delete
        } else {
            $visit->status = 'cancelled';
            $visit->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => $patientName . ' removed from queue',
        ]);
    }
}
