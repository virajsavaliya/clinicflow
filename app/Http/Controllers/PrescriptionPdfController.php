<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrescriptionPdfController extends Controller
{
    /**
     * Generate PDF for a prescription.
     */
    public function generate(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        
        $prescription->load(['visit.patient', 'visit.doctor.doctorProfile', 'items.medicine']);
        
        $data = $this->preparePdfData($prescription);
        
        $pdf = Pdf::loadView('pdf.prescription', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'prescription_' . $prescription->id . '_' . now()->format('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * View PDF in browser.
     */
    public function view(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        
        $prescription->load(['visit.patient', 'visit.doctor.doctorProfile', 'items.medicine']);
        
        $data = $this->preparePdfData($prescription);
        
        $pdf = Pdf::loadView('pdf.prescription', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('prescription.pdf');
    }
    
    /**
     * Generate PDF from visit ID.
     */
    public function fromVisit(Visit $visit)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;
        
        if ($visit->doctor_id !== $doctorId) {
            abort(403, 'Unauthorized');
        }
        
        $prescription = $visit->prescription;
        
        if (!$prescription) {
            return back()->with('error', 'No prescription found for this visit');
        }
        
        return $this->generate($prescription);
    }
    
    /**
     * Print prescription - opens in new tab for printing.
     */
    public function print(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        
        $prescription->load(['visit.patient', 'visit.doctor.doctorProfile', 'items.medicine']);
        
        $data = $this->preparePdfData($prescription);
        $data['printMode'] = true;
        
        return view('pdf.prescription-print', $data);
    }
    
    /**
     * Authorize access to prescription.
     */
    private function authorizeAccess(Prescription $prescription): void
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;
        
        if ($prescription->visit->doctor_id !== $doctorId) {
            abort(403, 'Unauthorized to access this prescription');
        }
    }
    
    /**
     * Prepare data for PDF.
     */
    private function preparePdfData(Prescription $prescription): array
    {
        $visit = $prescription->visit;
        $patient = $visit->patient;
        $doctor = $visit->doctor;
        $doctorProfile = $doctor->doctorProfile;
        
        // Generate QR code with prescription info
        $qrData = json_encode([
            'rx_id' => $prescription->id,
            'patient' => $patient->name,
            'doctor' => $doctor->name,
            'date' => $prescription->created_at->format('Y-m-d'),
            'verify' => route('prescription.verify', $prescription->id),
        ]);
        
        // We'll use a simple text-based verification code instead of QR for simplicity
        $verificationCode = strtoupper(substr(md5($prescription->id . $prescription->created_at), 0, 8));
        
        return [
            'prescription' => $prescription,
            'patient' => $patient,
            'doctor' => $doctor,
            'doctorProfile' => $doctorProfile,
            'visit' => $visit,
            'items' => $prescription->items,
            'verificationCode' => $verificationCode,
            'clinicName' => config('app.name', 'ClinicFlow'),
            'clinicAddress' => config('clinic.address', '123 Medical Street, Healthcare City'),
            'clinicPhone' => config('clinic.phone', '+91 98765 43210'),
            'clinicEmail' => config('clinic.email', 'contact@clinicflow.com'),
        ];
    }
    
    /**
     * Verify prescription authenticity.
     */
    public function verify($id)
    {
        $prescription = Prescription::with(['visit.patient', 'visit.doctor'])->find($id);
        
        if (!$prescription) {
            return view('pdf.verify-failed');
        }
        
        return view('pdf.verify-success', [
            'prescription' => $prescription,
            'patient' => $prescription->visit->patient,
            'doctor' => $prescription->visit->doctor,
        ]);
    }
}
