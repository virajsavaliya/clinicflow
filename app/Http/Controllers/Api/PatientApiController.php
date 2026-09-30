<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientApiController extends Controller
{
    /**
     * Update a patient's details.
     *
     * @param Request $request
     * @param int $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|min:2',
                'phone' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]{10}$/',
                    Rule::unique('patients', 'phone')->ignore($patientId),
                ],
                'birthdate' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string|max:500',
            ], [
                'phone.regex' => 'Phone number must be exactly 10 digits',
                'phone.unique' => 'This phone number is already registered with another patient',
                'name.min' => 'Patient name must be at least 2 characters',
                'birthdate.before' => 'Birthdate must be in the past',
                'birthdate.date' => 'Please enter a valid date',
            ]);

            $patient->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found',
                'error' => 'NOT_FOUND',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating patient: ' . $e->getMessage(),
                'error' => 'UPDATE_ERROR',
            ], 500);
        }
    }

    /**
     * Delete a patient (soft delete - archive).
     *
     * @param int $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($patientId)
    {
        try {
            $patient = Patient::withTrashed()->findOrFail($patientId);
            
            // If already deleted, restore and then delete again (toggle)
            if ($patient->trashed()) {
                $patient->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => "Patient '{$patient->name}' permanently deleted",
                ]);
            }

            // Soft delete (archive) the patient
            $patientName = $patient->name;
            $patient->delete();

            return response()->json([
                'success' => true,
                'message' => "Patient '$patientName' archived successfully (can be restored)",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found',
                'error' => 'NOT_FOUND',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving patient: ' . $e->getMessage(),
                'error' => 'DELETE_ERROR',
            ], 500);
        }
    }
}
