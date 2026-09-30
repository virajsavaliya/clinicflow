<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
 |--------------------------------------------------------------------------
 | Web Routes
 |--------------------------------------------------------------------------
 |
 | Here is where you can register web routes for your application. These
 | routes are loaded by the RouteServiceProvider and all of them will
 | be assigned to the "web" middleware group. Make something great!
 |
 */

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'doctor') {
            return redirect('/doctor/dashboard');
        }
        elseif ($user->role === 'reception') {
            return redirect('/reception/dashboard');
        }
        elseif ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }
    }
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
            // If already authenticated, redirect to home
            if (auth()->check()) {
                return redirect('/');
            }
            return view('auth.login');
        }
        )->name('login')->withoutMiddleware('guest');

        Route::post('/login', function (\Illuminate\Http\Request $request) {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/');
            }

            return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
        )->middleware('throttle:5,1')->name('login.post'); // Rate limit: 5 attempts per minute
    });

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Reception routes
Route::middleware(['auth', 'role:reception'])->prefix('reception')->group(function () {
    Route::get('/dashboard', [ReceptionController::class , 'dashboard'])->name('reception.dashboard');
    Route::get('/test-simple', function () {
            return view('reception.test-simple');
        }
        )->name('reception.test');
        Route::get('/patients', [ReceptionController::class , 'patients'])->name('reception.patients');
        Route::get('/pharmacy', [ReceptionController::class , 'pharmacy'])->name('reception.pharmacy');
        Route::post('/enqueue-patient', [ReceptionController::class , 'enqueuePatient'])->name('reception.enqueue');
        Route::get('/search-patients', [ReceptionController::class , 'searchPatients'])->name('reception.search-patients');
        Route::get('/search-by-phone', [ReceptionController::class , 'searchByPhone'])->name('reception.search-by-phone');
        Route::post('/add-medicine', [ReceptionController::class , 'addMedicine'])->name('reception.add-medicine');
        Route::post('/adjust-stock', [ReceptionController::class , 'adjustStock'])->name('reception.adjust-stock');
        Route::get('/notifications', [ReceptionController::class , 'notifications'])->name('reception.notifications');

        // Notification Settings (Automated Reminders)
        Route::get('/notification-settings', [ReceptionController::class , 'notificationSettings'])->name('reception.notifications.settings');
        Route::post('/notification-settings', [ReceptionController::class , 'saveNotificationSettings'])->name('reception.notifications.save');

        // Appointments (Reception creates appointments for patients)
        Route::get('/appointments', [App\Http\Controllers\AppointmentController::class , 'receptionIndex'])->name('reception.appointments');
        Route::get('/appointments/calendar', [App\Http\Controllers\AppointmentController::class , 'receptionCalendar'])->name('reception.appointments.calendar');
        Route::post('/appointments', [App\Http\Controllers\AppointmentController::class , 'store'])->name('reception.appointments.store');
        Route::put('/appointments/{id}/status', [App\Http\Controllers\AppointmentController::class , 'updateStatus'])->name('reception.appointments.status');
        Route::put('/appointments/{id}/reschedule', [App\Http\Controllers\AppointmentController::class , 'reschedule'])->name('reception.appointments.reschedule');
        Route::delete('/appointments/{id}', [App\Http\Controllers\AppointmentController::class , 'destroy'])->name('reception.appointments.delete');
        Route::get('/appointments/slots', [App\Http\Controllers\AppointmentController::class , 'getAvailableSlots'])->name('reception.appointments.slots');
    });

// Doctor routes
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->group(function () {
    Route::get('/dashboard', [DoctorController::class , 'dashboard'])->name('doctor.dashboard');
    Route::get('/visit/{visitId}', [DoctorController::class , 'openVisit'])->name('doctor.visit');
    Route::get('/patient/{patientId}/history', [ReceptionController::class , 'getPatientHistory'])->name('doctor.patient-history');
    Route::post('/visit/{visitId}/diagnosis', [DoctorController::class , 'addDiagnosis'])->name('doctor.diagnosis');

    // Patient history search
    Route::get('/patients', [DoctorController::class , 'patientHistory'])->name('doctor.patients');

    // Clinic settings routes
    Route::get('/clinic/settings', [DoctorController::class , 'getClinicSettings'])->name('doctor.clinic.settings');
    Route::post('/clinic/settings', [DoctorController::class , 'manageClinicSettings'])->name('doctor.clinic.update');
    Route::delete('/clinic/logo', [DoctorController::class , 'deleteClinicLogo'])->name('doctor.clinic.delete-logo');

    // Doctor charges CRUD routes
    Route::get('/charges', [DoctorController::class , 'getDoctorCharges'])->name('doctor.charges.list');
    Route::post('/charges', [DoctorController::class , 'createDoctorCharge'])->name('doctor.charges.create');
    Route::put('/charges/{chargeId}', [DoctorController::class , 'updateDoctorCharge'])->name('doctor.charges.update');
    Route::delete('/charges/{chargeId}', [DoctorController::class , 'deleteDoctorCharge'])->name('doctor.charges.delete');

    // Prescription and visit completion
    Route::post('/visit/{visitId}/finalize-prescription', [PrescriptionController::class , 'finalizePrescription'])->name('doctor.finalize-prescription');
    Route::get('/visit/{visitId}/prescription', [PrescriptionController::class , 'getPrescription'])->name('doctor.get-prescription');
    Route::post('/visit/{visitId}/complete', [DoctorController::class , 'completeVisit'])->name('doctor.complete-visit');

    // Reception user management (doctors can create reception users)
    Route::get('/receptions', [DoctorController::class , 'receptions'])->name('doctor.receptions');
    Route::get('/receptions/create', [DoctorController::class , 'createReception'])->name('doctor.receptions.create');
    Route::post('/receptions', [DoctorController::class , 'storeReception'])->name('doctor.receptions.store');
    Route::get('/receptions/{id}/edit', [DoctorController::class , 'editReception'])->name('doctor.receptions.edit');
    Route::put('/receptions/{id}', [DoctorController::class , 'updateReception'])->name('doctor.receptions.update');
    Route::delete('/receptions/{id}', [DoctorController::class , 'deleteReception'])->name('doctor.receptions.delete');

    // Medicine inventory management (uses API endpoints in routes/api.php)
    Route::get('/medicines', [DoctorController::class , 'medicines'])->name('doctor.medicines');
    Route::get('/medicine', [DoctorController::class , 'medicines'])->name('doctor.medicine');

    // Prescription PDF
    Route::get('/prescription/{prescription}/pdf', [App\Http\Controllers\PrescriptionPdfController::class , 'generate'])->name('prescription.pdf');
    Route::get('/prescription/{prescription}/view', [App\Http\Controllers\PrescriptionPdfController::class , 'view'])->name('prescription.view');
    Route::get('/prescription/{prescription}/print', [App\Http\Controllers\PrescriptionPdfController::class , 'print'])->name('prescription.print');
    Route::get('/visit/{visit}/prescription-pdf', [App\Http\Controllers\PrescriptionPdfController::class , 'fromVisit'])->name('prescription.from-visit');

    // Appointments & Scheduling
    Route::get('/appointments', [App\Http\Controllers\AppointmentController::class , 'index'])->name('doctor.appointments');
    Route::get('/appointments/calendar', [App\Http\Controllers\AppointmentController::class , 'doctorCalendar'])->name('doctor.appointments.calendar');
    Route::get('/appointments/slots', [App\Http\Controllers\AppointmentController::class , 'getAvailableSlots'])->name('doctor.appointments.slots');
    Route::post('/appointments', [App\Http\Controllers\AppointmentController::class , 'store'])->name('doctor.appointments.store');
    Route::put('/appointments/{id}/status', [App\Http\Controllers\AppointmentController::class , 'updateStatus'])->name('doctor.appointments.status');
    Route::put('/appointments/{id}/reschedule', [App\Http\Controllers\AppointmentController::class , 'reschedule'])->name('doctor.appointments.reschedule');
    Route::delete('/appointments/{id}', [App\Http\Controllers\AppointmentController::class , 'cancel'])->name('doctor.appointments.cancel');
    Route::post('/appointments/{id}/convert', [App\Http\Controllers\AppointmentController::class , 'convertToVisit'])->name('doctor.appointments.convert');

    // Patient search (autocomplete)
    Route::get('/patients/search', [App\Http\Controllers\DoctorController::class , 'searchPatients'])->name('doctor.patients.search');

    // Doctor Schedule Management
    Route::get('/schedule', [App\Http\Controllers\AppointmentController::class , 'schedule'])->name('doctor.schedule');
    Route::post('/schedule', [App\Http\Controllers\AppointmentController::class , 'updateSchedule'])->name('doctor.schedule.update');
    Route::post('/holidays', [App\Http\Controllers\AppointmentController::class , 'addHoliday'])->name('doctor.holidays.add');
    Route::delete('/holidays/{id}', [App\Http\Controllers\AppointmentController::class , 'removeHoliday'])->name('doctor.holidays.remove');

    // Reports & Analytics
    Route::get('/reports', [App\Http\Controllers\ReportsController::class , 'index'])->name('doctor.reports');
    Route::get('/reports/export', [App\Http\Controllers\ReportsController::class , 'export'])->name('doctor.reports.export');

    // Inventory Alerts
    Route::get('/inventory-alerts', [App\Http\Controllers\InventoryAlertController::class , 'index'])->name('doctor.inventory-alerts');
    Route::get('/api/inventory-alerts', [App\Http\Controllers\InventoryAlertController::class , 'getAlerts'])->name('doctor.inventory-alerts.api');
    Route::post('/inventory-alerts/mark-ordered/{medicine}', [App\Http\Controllers\InventoryAlertController::class , 'markOrdered'])->name('doctor.inventory-alerts.mark-ordered');
});

// Bill routes
Route::middleware('auth')->prefix('bills')->group(function () {
    Route::get('/{billId}', [BillController::class , 'getBill'])->name('bills.get');
    Route::get('/{billId}/view', [BillController::class , 'viewInvoice'])->name('bills.view');
    Route::get('/{billId}/pdf', [BillController::class , 'downloadInvoicePdf'])->name('bills.pdf');
    Route::post('/{billId}/payment', [BillController::class , 'recordPayment'])->name('bills.payment');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class , 'dashboard'])->name('admin.dashboard');

    // Doctor management (admins can ONLY create and manage doctors)
    Route::get('/doctors', [AdminController::class , 'doctors'])->name('admin.doctors');
    Route::get('/doctors/create', [AdminController::class , 'createDoctor'])->name('admin.doctors.create');
    Route::post('/doctors', [AdminController::class , 'storeDoctor'])->name('admin.doctors.store');
    Route::get('/doctors/{doctor}/edit', [AdminController::class , 'editDoctor'])->name('admin.doctors.edit');
    Route::put('/doctors/{doctor}', [AdminController::class , 'updateDoctor'])->name('admin.doctors.update');

    // User deletion (can delete doctors and admins)
    Route::delete('/users/{user}', [AdminController::class , 'deleteUser'])->name('admin.users.delete');
});

// Public prescription verification (no auth required)
Route::get('/prescription/verify/{id}', [App\Http\Controllers\PrescriptionPdfController::class , 'verify'])->name('prescription.verify');