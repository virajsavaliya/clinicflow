<?php

use App\Http\Controllers\Api\MedicineApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\QueueApiController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Use 'auth' middleware for web-based AJAX calls with CSRF token
Route::middleware('auth')->group(function () {
    // Queue API endpoints
    Route::get('/queue', [QueueApiController::class, 'getQueue'])->name('api.queue.get');
    Route::get('/visits/{visitId}', [QueueApiController::class, 'getVisit'])->name('api.visit.get');
    Route::delete('/queue/{visitId}', [QueueApiController::class, 'removeFromQueue'])->name('api.queue.delete');

    // Patient API endpoints
    Route::put('/patients/{patientId}', [PatientApiController::class, 'update'])->name('api.patients.update');
    Route::delete('/patients/{patientId}', [PatientApiController::class, 'delete'])->name('api.patients.delete');

    // Appointment API endpoints
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('api.appointments.show');

    // Medicine API endpoints
    Route::get('/medicines/search', [MedicineApiController::class, 'search'])->name('api.medicines.search');
    Route::get('/medicines/{medicineId}/stock', [MedicineApiController::class, 'getStock'])->name('api.medicines.stock');
    Route::post('/medicines/{medicineId}/stock', [MedicineApiController::class, 'addStock'])->name('api.medicines.add-stock');
    Route::post('/medicines/adjust-stock', [MedicineApiController::class, 'adjustStock'])->name('api.medicines.adjust-stock');
    Route::get('/medicines', [MedicineApiController::class, 'getAll'])->name('api.medicines.all');
    Route::post('/medicines', [MedicineApiController::class, 'store'])->name('api.medicines.store');
    Route::put('/medicines/{medicineId}', [MedicineApiController::class, 'update'])->name('api.medicines.update');
    Route::delete('/medicines/{medicineId}', [MedicineApiController::class, 'delete'])->name('api.medicines.delete');

    // Notification API endpoints
    Route::post('/notifications', [NotificationApiController::class, 'store'])->name('api.notifications.store');
    Route::get('/notifications/unread', [NotificationApiController::class, 'getUnread'])->name('api.notifications.unread');
    Route::post('/notifications/{notificationId}/read', [NotificationApiController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [NotificationApiController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});

