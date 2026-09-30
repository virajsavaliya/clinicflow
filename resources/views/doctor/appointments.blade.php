@extends('layouts.app')

@section('title', 'Appointments - ClinicFlow')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Appointments</h4>
        <p class="text-muted mb-0 mt-1 fs-7">View and manage your appointment schedule</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white border rounded p-1 d-flex shadow-sm">
            <button
                class="btn btn-sm px-3 {{ $view === 'day' ? 'btn-light border shadow-sm fw-bold' : 'btn-link text-decoration-none text-muted' }}"
                onclick="changeView('day')">Day</button>
            <button
                class="btn btn-sm px-3 {{ $view === 'week' ? 'btn-light border shadow-sm fw-bold' : 'btn-link text-decoration-none text-muted' }}"
                onclick="changeView('week')">Week</button>
            <button
                class="btn btn-sm px-3 {{ $view === 'month' ? 'btn-light border shadow-sm fw-bold' : 'btn-link text-decoration-none text-muted' }}"
                onclick="changeView('month')">Month</button>
            <button
                class="btn btn-sm px-3 btn-link text-decoration-none text-muted"
                onclick="window.location.href='{{ route('doctor.appointments.calendar') }}'">
                <i class="bi bi-grid-3x3-gap me-1"></i>Calendar</button>
        </div>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="bi bi-plus-lg me-1"></i> New Appointment
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-dark text-white rounded p-3 me-3 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-calendar3 fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['total'] }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Today's Total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded p-3 me-3 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['confirmed'] }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Confirmed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded p-3 me-3 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['pending'] }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-purple text-white rounded p-3 me-3 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px; background-color: #6f42c1;">
                    <i class="bi bi-check-all fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['completed'] }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Completed</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Agenda View -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="navigateDate(-1)"><i
                            class="bi bi-chevron-left"></i></button>
                    <span class="fs-6 fw-bold text-dark px-2" style="min-width: 140px; text-align: center;">
                        {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                    </span>
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="navigateDate(1)"><i
                            class="bi bi-chevron-right"></i></button>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary px-3" onclick="goToToday()">Today</button>
                </div>
            </div>

            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                @if($appointments->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h5 class="text-dark fw-bold mb-1">No appointments</h5>
                    <p class="text-muted mb-0">No appointments scheduled for this {{ $view }}.</p>
                </div>
                @else
                @php
                // Group appointments by date
                $groupedAppointments = $appointments->groupBy(function($apt) {
                return \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d');
                });
                @endphp

                @foreach($groupedAppointments as $dateKey => $dayAppointments)
                <!-- Date Header -->
                <div
                    class="bg-light border-bottom px-4 py-2 sticky-top d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-uppercase fs-7 text-muted">
                        {{ \Carbon\Carbon::parse($dateKey)->format('l, F j, Y') }}
                        @if(\Carbon\Carbon::parse($dateKey)->isToday())
                        <span class="badge bg-primary ms-2">Today</span>
                        @endif
                    </h6>
                    <span class="badge bg-secondary rounded-pill">{{ $dayAppointments->count() }}</span>
                </div>

                <!-- Appointments List -->
                <div class="list-group list-group-flush">
                    @foreach($dayAppointments as $apt)
                    @php
                    $statusColor = 'secondary';
                    if ($apt->status === 'confirmed') $statusColor = 'success';
                    if ($apt->status === 'scheduled') $statusColor = 'primary';
                    if ($apt->status === 'completed') $statusColor = 'dark';
                    @endphp
                    <button type="button" class="list-group-item list-group-item-action px-4 py-3 border-bottom"
                        onclick="viewAppointment({{ $apt->id }})">
                        <div class="row align-items-center g-3">
                            <!-- Time Column -->
                            <div class="col-md-2 col-3 border-end">
                                <div class="fw-bold text-dark fs-6">{{
                                    \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                                <div class="fs-7 text-muted">{{
                                    \Carbon\Carbon::parse($apt->appointment_time)->addMinutes(30)->format('h:i A') }}
                                </div>
                            </div>

                            <!-- Patient Info Column -->
                            <div class="col-md-7 col-6">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-dark me-2">{{ $apt->patient->name }}</h6>
                                    <span
                                        class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} border-opacity-25 rounded-pill fs-8 px-2 py-1 text-uppercase">{{
                                        $apt->status }}</span>
                                </div>
                                @if($apt->reason)
                                <div class="text-muted fs-7 text-truncate"><i class="bi bi-chat-left-text me-1"></i>{{
                                    $apt->reason }}</div>
                                @else
                                <div class="text-muted fs-7"><i class="bi bi-dash"></i> No specific reason provided
                                </div>
                                @endif
                            </div>

                            <!-- Actions/Status Indicator -->
                            <div class="col-md-3 col-3 text-end">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-primary"
                                    style="width: 35px; height: 35px; transition: background 0.2s;">
                                    <i class="bi bi-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-3">
        <!-- Upcoming Appointments Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming
                    Appointments</h6>
            </div>
            <div class="card-body p-0" style="max-height: 420px; overflow-y: auto;">
                <div class="list-group list-group-flush">
                    @forelse($upcomingAppointments as $apt)
                    @php
                    $statusColor = 'secondary';
                    if ($apt->status === 'confirmed') $statusColor = 'success';
                    if ($apt->status === 'scheduled') $statusColor = 'primary';
                    @endphp
                    <button type="button" class="list-group-item list-group-item-action p-3"
                        onclick="viewAppointment({{ $apt->id }})">
                        <div class="d-flex align-items-start gap-2">
                            <div class="bg-dark text-white rounded text-center d-flex flex-column justify-content-center"
                                style="width: 40px; height: 40px; flex-shrink: 0;">
                                <span class="fw-bold lh-1" style="font-size:0.85rem;">{{ $apt->appointment_date->format('d') }}</span>
                                <span class="text-uppercase" style="font-size: 0.55rem;">{{
                                    $apt->appointment_date->format('M') }}</span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size:0.8rem;">{{ $apt->patient->name }}</h6>
                                <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                    <small class="text-muted" style="font-size:0.7rem;"><i class="bi bi-clock me-1"></i>{{
                                        \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</small>
                                    <small class="text-{{ $statusColor }} fw-semibold text-capitalize" style="font-size:0.7rem;">· {{ $apt->status }}</small>
                                </div>
                            </div>
                        </div>
                    </button>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox fs-3 mb-2 opacity-50 d-block"></i>
                        <p class="mb-0 fs-7">No upcoming appointments in the next 7 days.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Appointment Modal -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-calendar-plus me-2 text-primary"></i>New
                    Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('doctor.appointments.store') }}" method="POST" id="newAppointmentForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-dark fs-7">Patient <span
                                class="text-danger">*</span></label>
                        <input type="text" id="patientSearchInput" class="form-control"
                            placeholder="Type patient name..." autocomplete="off">
                        <input type="hidden" name="patient_id" id="patientIdHidden" required>
                        <div id="patientSuggestions" class="list-group position-absolute w-100 shadow-sm"
                            style="z-index: 9999; top: 100%; display: none; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="appointment_date" id="newAppointmentDate" class="form-control"
                                required min="{{ date('Y-m-d') }}" onchange="fetchAvailableSlots()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Time <span
                                    class="text-danger">*</span></label>
                            <select name="appointment_time" id="newAppointmentTime" class="form-select" required
                                disabled>
                                <option value="">Select Date First</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="Brief reason for visit">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-dark fs-7">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="Any additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium" id="btnSubmitNewAppointment">
                        Create Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="bi bi-calendar-event me-2 text-primary"></i>Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="appointmentDetails">
                <!-- Filled by JS -->
            </div>
            <div class="modal-footer border-top bg-light" id="appointmentActions">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i
                        class="bi bi-calendar-check me-2 text-primary"></i>Reschedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-7"><i class="bi bi-calendar3 me-1 text-muted"></i> New
                        Date</label>
                    <input type="date" id="rescheduleDate" class="form-control" min="{{ date('Y-m-d') }}"
                        onchange="fetchRescheduleSlots()">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold text-dark fs-7"><i class="bi bi-clock me-1 text-muted"></i> New
                        Time</label>
                    <select id="rescheduleTime" class="form-select" disabled>
                        <option value="">Select Date First</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-medium" onclick="submitReschedule()"
                    id="btnSubmitReschedule">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentDate = '{{ $date }}';
    let currentView = '{{ $view }}';

    function changeView(view) {
        window.location.href = `{{ route('doctor.appointments') }}?date=${currentDate}&view=${view}`;
    }

    function navigateDate(direction) {
        const date = new Date(currentDate);
        if (currentView === 'day') {
            date.setDate(date.getDate() + direction);
        } else if (currentView === 'week') {
            date.setDate(date.getDate() + (direction * 7));
        } else {
            date.setMonth(date.getMonth() + direction);
        }
        const newDate = date.toISOString().split('T')[0];
        window.location.href = `{{ route('doctor.appointments') }}?date=${newDate}&view=${currentView}`;
    }

    function goToToday() {
        window.location.href = `{{ route('doctor.appointments') }}?view=${currentView}`;
    }

    let selectedAppointment = null;
    let viewModalInstance = null;
    let rescheduleModalInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        viewModalInstance = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
        rescheduleModalInstance = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    });

    async function viewAppointment(id) {
        try {
            const detailsDiv = document.getElementById('appointmentDetails');
            const actionsDiv = document.getElementById('appointmentActions');

            detailsDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
            actionsDiv.innerHTML = '';
            viewModalInstance.show();

            const response = await axios.get(`/api/appointments/${id}`);
            const apt = response.data;

            let statusColor = 'secondary';
            if (apt.status === 'confirmed') statusColor = 'success';
            if (apt.status === 'scheduled') statusColor = 'primary';
            if (apt.status === 'completed') statusColor = 'dark';

            // Display appointment details
            detailsDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center bg-white border rounded p-3 mb-4 shadow-sm">
                <div>
                    <h6 class="mb-1 text-dark fw-bold fs-5"><i class="bi bi-person-circle me-2 text-primary"></i>${apt.patient.name}</h6>
                    ${apt.patient.phone ? `<div class="text-muted fs-7"><i class="bi bi-telephone me-1"></i>${apt.patient.phone}</div>` : ''}
                </div>
                <span class="badge bg-${statusColor} bg-opacity-10 text-${statusColor} border border-${statusColor} border-opacity-25 px-3 py-2 text-uppercase fs-7 rounded-pill">${apt.status}</span>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted fs-8 fw-bold mb-1 text-uppercase">Date</div>
                        <div class="fw-bold text-dark fs-6"><i class="bi bi-calendar3 text-primary me-2"></i>${new Date(apt.appointment_date).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="text-muted fs-8 fw-bold mb-1 text-uppercase">Time</div>
                        <div class="fw-bold text-dark fs-6"><i class="bi bi-clock text-primary me-2"></i>${new Date('1970-01-01T' + apt.appointment_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                </div>
            </div>
            
            ${apt.reason ? `
                <div class="mb-3">
                    <div class="text-dark fw-bold fs-7 mb-2"><i class="bi bi-chat-left-text text-muted me-2"></i>Reason for Visit</div>
                    <div class="bg-light p-3 rounded border fs-7 text-dark">${apt.reason}</div>
                </div>
            ` : ''}
            
            ${apt.notes ? `
                <div>
                    <div class="text-dark fw-bold fs-7 mb-2"><i class="bi bi-journal-text text-muted me-2"></i>Notes</div>
                    <div class="bg-light p-3 rounded border fs-7 text-dark">${apt.notes}</div>
                </div>
            ` : ''}
        `;

            // Add action buttons
            if (apt.status !== 'cancelled' && apt.status !== 'completed') {
                actionsDiv.innerHTML = `
                <button type="button" class="btn btn-outline-danger fw-medium" onclick="cancelFromView(${apt.id})">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-white border fw-medium" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary fw-medium px-3 ms-2" onclick="rescheduleFromView(${apt.id})">
                        Reschedule
                    </button>
                </div>
            `;
            } else {
                actionsDiv.innerHTML = `
                <div class="w-100 text-end">
                    <button type="button" class="btn btn-white border fw-medium px-4" data-bs-dismiss="modal">Close</button>
                </div>
            `;
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('appointmentDetails').innerHTML = `
            <div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Error loading appointment details</div>
        `;
        }
    }

    function rescheduleFromView(appointmentId) {
        selectedAppointment = appointmentId;
        document.getElementById('rescheduleDate').value = '';
        const rescheduleTimeEl = document.getElementById('rescheduleTime');
        rescheduleTimeEl.innerHTML = '<option value="">Select Date First</option>';
        rescheduleTimeEl.disabled = true;
        viewModalInstance.hide();
        rescheduleModalInstance.show();
    }

    function submitReschedule() {
        if (!selectedAppointment) return;

        const date = document.getElementById('rescheduleDate').value;
        const timeEl = document.getElementById('rescheduleTime');
        const time = timeEl.value;

        if (!date || !time) {
            alert('Please select both date and time');
            return;
        }

        const submitBtn = document.getElementById('btnSubmitReschedule');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Wait...';
        submitBtn.disabled = true;

        axios.put(`/doctor/appointments/${selectedAppointment}/reschedule`, {
            appointment_date: date,
            appointment_time: time
        })
            .then(response => {
                if (response.data.success) {
                    const appointment = response.data.appointment;
                    if (appointment && appointment.patient && appointment.patient.phone) {
                        showNotificationPrompt(appointment, 'rescheduled');
                    } else {
                        location.reload();
                    }
                } else {
                    alert(response.data.message || 'Error rescheduling appointment');
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const msg = error.response?.data?.message || error.response?.data?.errors
                    ? (error.response.data.message || Object.values(error.response.data.errors).flat().join('\n'))
                    : 'An error occurred. Please try again.';
                alert(msg);
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            });
    }

    function cancelFromView(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            updateAppointmentStatus(appointmentId, 'cancelled');
        }
    }

    function updateAppointmentStatus(appointmentId, status) {
        axios.put(`/doctor/appointments/${appointmentId}/status`, { status: status })
            .then(response => {
                if (response.data.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }

    // Reschedule slot loader
    function fetchRescheduleSlots() {
        const date = document.getElementById('rescheduleDate').value;
        const timeSelect = document.getElementById('rescheduleTime');

        if (!date) {
            timeSelect.innerHTML = '<option value="">Select Date First</option>';
            timeSelect.disabled = true;
            return;
        }

        timeSelect.innerHTML = '<option value="">Loading slots...</option>';
        timeSelect.disabled = true;

        const excludeParam = selectedAppointment ? `&exclude_id=${selectedAppointment}` : '';
        axios.get(`{{ route('doctor.appointments.slots') }}?date=${date}${excludeParam}`)
            .then(response => {
                timeSelect.innerHTML = '<option value="">Select Time</option>';
                if (response.data.slots && response.data.slots.length > 0) {
                    response.data.slots.forEach(slot => {
                        const formatted = new Date('1970-01-01T' + slot).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        timeSelect.innerHTML += `<option value="${slot}">${formatted}</option>`;
                    });
                    timeSelect.disabled = false;
                } else {
                    timeSelect.innerHTML = '<option value="">' + (response.data.message || 'No slots available for this date') + '</option>';
                }
            })
            .catch(() => {
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    // New Appointment Handlers
    function fetchAvailableSlots() {
        const dateInput = document.getElementById('newAppointmentDate');
        const timeSelect = document.getElementById('newAppointmentTime');
        const date = dateInput.value;

        if (!date) {
            timeSelect.innerHTML = '<option value="">Select Date First</option>';
            timeSelect.disabled = true;
            return;
        }

        // Show loading state
        timeSelect.innerHTML = '<option value="">Loading slots...</option>';
        timeSelect.disabled = true;

        axios.get(`{{ route('doctor.appointments.slots') }}?date=${date}`)
            .then(response => {
                timeSelect.innerHTML = '<option value="">Select Time</option>';

                if (response.data.slots && response.data.slots.length > 0) {
                    response.data.slots.forEach(slot => {
                        // slot is a string like "09:00"
                        const formatted = new Date('1970-01-01T' + slot).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        timeSelect.innerHTML += `<option value="${slot}">${formatted}</option>`;
                    });
                    timeSelect.disabled = false;
                } else {
                    timeSelect.innerHTML = '<option value="">No slots available</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching slots:', error);
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    // Handle new appointment form submission with AJAX
    document.addEventListener('DOMContentLoaded', function () {
        const newAptForm = document.getElementById('newAppointmentForm');
        if (newAptForm) {
            newAptForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const patientId = document.getElementById('patientIdHidden').value;
                const date = document.getElementById('newAppointmentDate').value;
                const time = document.getElementById('newAppointmentTime').value;

                if (!patientId) { showToast('Please select a patient from the list.', 'error'); return; }
                if (!date)      { showToast('Please select a date.', 'error'); return; }
                if (!time)      { showToast('Please select a time slot.', 'error'); return; }

                const submitBtn = document.getElementById('btnSubmitNewAppointment');
                const origText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                submitBtn.disabled = true;

                const formData = new FormData(newAptForm);
                axios.post(newAptForm.action, formData)
                    .then(response => {
                        if (response.data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('newAppointmentModal')).hide();
                            showToast('Appointment scheduled successfully!', 'success');
                            
                            const appointment = response.data.appointment;
                            if (appointment && appointment.patient && appointment.patient.phone) {
                                showNotificationPrompt(appointment, 'created');
                            } else {
                                setTimeout(() => location.reload(), 1500);
                            }
                        } else {
                            showToast(response.data.message || 'Failed to create appointment.', 'error');
                            submitBtn.innerHTML = origText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        const msg = err.response?.data?.message || 'An error occurred. Please try again.';
                        showToast(msg, 'error');
                        submitBtn.innerHTML = origText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Patient Autocomplete
        const patientInput = document.getElementById('patientSearchInput');
        const patientHidden = document.getElementById('patientIdHidden');
        const suggestionBox = document.getElementById('patientSuggestions');
        let searchTimeout = null;

        if (patientInput) {
            patientInput.addEventListener('input', function () {
                const query = this.value.trim();
                patientHidden.value = ''; // Reset hidden value on new input

                if (query.length < 2) {
                    suggestionBox.style.display = 'none';
                    suggestionBox.innerHTML = '';
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    axios.get(`{{ route('doctor.patients.search') }}?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            suggestionBox.innerHTML = '';
                            const patients = response.data.patients || [];

                            if (patients.length === 0) {
                                suggestionBox.innerHTML = '<div class="list-group-item text-muted">No patients found</div>';
                            } else {
                                patients.forEach(p => {
                                    const item = document.createElement('button');
                                    item.type = 'button';
                                    item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                                    item.innerHTML = `<span class="fw-semibold">${p.name}</span><span class="text-muted small">${p.phone || ''}</span>`;
                                    item.addEventListener('click', () => {
                                        patientInput.value = p.name;
                                        patientHidden.value = p.id;
                                        suggestionBox.style.display = 'none';
                                    });
                                    suggestionBox.appendChild(item);
                                });
                            }
                            suggestionBox.style.display = 'block';
                        })
                        .catch(() => {
                            suggestionBox.style.display = 'none';
                        });
                }, 300);
            });

            // Hide on outside click
            document.addEventListener('click', function (e) {
                if (!patientInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.style.display = 'none';
                }
            });

            // Reset on modal close
            document.getElementById('newAppointmentModal').addEventListener('hidden.bs.modal', function () {
                patientInput.value = '';
                patientHidden.value = '';
                suggestionBox.style.display = 'none';
                document.getElementById('newAppointmentDate').value = '';
                const timeSelect = document.getElementById('newAppointmentTime');
                timeSelect.innerHTML = '<option value="">Select Date First</option>';
                timeSelect.disabled = true;
            });
        }
    });

    function showToast(message, type) {
        const colors = { success: '#22c55e', error: '#ef4444', info: '#3b82f6' };
        const icons  = { success: 'check-circle-fill', error: 'exclamation-circle-fill', info: 'info-circle-fill' };
        const el = document.createElement('div');
        el.style.cssText = `position:fixed;top:20px;right:20px;padding:.875rem 1.25rem;background:${colors[type]||colors.info};color:#fff;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.18);z-index:99999;font-weight:600;font-size:.875rem;display:flex;align-items:center;gap:.5rem;`;
        el.innerHTML = `<i class="bi bi-${icons[type]||icons.info}"></i> ${message}`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; setTimeout(() => el.remove(), 300); }, 3000);
    }
function showNotificationPrompt(appointment, action) {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
    `;
    
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: scaleIn 0.2s ease;
    `;
    
    const actionText = {
        created: 'Appointment Created',
        rescheduled: 'Appointment Rescheduled',
        cancelled: 'Appointment Cancelled'
    }[action] || 'Appointment Updated';
    
    const messageText = {
        created: 'Would you like to send a confirmation message to the patient?',
        rescheduled: 'Would you like to notify the patient about the rescheduled appointment?',
        cancelled: 'Would you like to notify the patient about the cancellation?'
    }[action] || 'Would you like to send a notification to the patient?';
    
    dialog.innerHTML = `
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <i class="bi bi-whatsapp" style="font-size: 2rem; color: white;"></i>
            </div>
            <h5 style="margin: 0 0 0.5rem; font-weight: 700; color: #1f2937;">${actionText}</h5>
            <p style="margin: 0; color: #6b7280; font-size: 0.95rem;">${messageText}</p>
        </div>
        
        <div style="background: #f9fafb; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid #3b82f6;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #1f2937;"><i class="bi bi-person-fill"></i> ${appointment.patient.name}</div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;"><i class="bi bi-telephone-fill"></i> ${appointment.patient.phone}</div>
            <div style="font-size: 0.875rem; color: #6b7280;"><i class="bi bi-calendar-check-fill"></i> ${new Date(appointment.appointment_date).toLocaleDateString('en-US', {weekday: 'short', month: 'short', day: 'numeric'})} at ${new Date('1970-01-01T' + appointment.appointment_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
            <button id="sendWhatsAppBtn" style="padding: 0.875rem; background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </button>
            <button id="sendSMSBtn" style="padding: 0.875rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="bi bi-chat-dots-fill"></i> SMS
            </button>
        </div>
        
        <button id="skipNotificationBtn" style="width: 100%; padding: 0.75rem; background: #f3f4f6; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
            Skip, Reload Page
        </button>
    `;
    
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);
    
    // Generate appropriate message based on action
    let messageTemplate;
    const appointmentDate = appointment.appointment_date;
    const appointmentTime = new Date('1970-01-01T' + appointment.appointment_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
    
    // Calculate date text (today/tomorrow/specific date)
    const aptDate = new Date(appointmentDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    let dateText;
    if (aptDate.toDateString() === today.toDateString()) {
        dateText = 'today';
    } else if (aptDate.toDateString() === tomorrow.toDateString()) {
        dateText = 'tomorrow';
    } else {
        const options = { weekday: 'long', month: 'long', day: 'numeric' };
        dateText = 'on ' + aptDate.toLocaleDateString('en-US', options);
    }
    
    if (action === 'created') {
        messageTemplate = `Hello ${appointment.patient.name},

Your appointment has been confirmed for ${dateText} at ${appointmentTime}.

ClinicFlow - Your Healthcare Partner

For any queries, please contact us.`;
    } else if (action === 'rescheduled') {
        messageTemplate = `Hello ${appointment.patient.name},

Your appointment has been rescheduled to ${dateText} at ${appointmentTime}.

ClinicFlow - Your Healthcare Partner

For any queries, please contact us.`;
    } else if (action === 'cancelled') {
        messageTemplate = `Hello ${appointment.patient.name},

Your appointment scheduled for ${dateText} at ${appointmentTime} has been cancelled.

ClinicFlow - Your Healthcare Partner

For any queries or to reschedule, please contact us.`;
    }
    
    // Button handlers
    document.getElementById('sendWhatsAppBtn').onclick = () => {
        overlay.remove();
        sendNotificationMessage(appointment.patient.phone, messageTemplate, 'whatsapp');
        setTimeout(() => location.reload(), 1000);
    };
    
    document.getElementById('sendSMSBtn').onclick = () => {
        overlay.remove();
        sendNotificationMessage(appointment.patient.phone, messageTemplate, 'sms');
        setTimeout(() => location.reload(), 1000);
    };
    
    document.getElementById('skipNotificationBtn').onclick = () => {
        overlay.remove();
        location.reload();
    };
    
    overlay.onclick = (e) => {
        if (e.target === overlay) {
            overlay.remove();
            location.reload();
        }
    };
}

// Send notification message (WhatsApp or SMS)
function sendNotificationMessage(phone, message, type) {
    if (!phone) return;
    
    // Format phone number
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    
    if (cleanPhone.length === 10) {
        cleanPhone = '91' + cleanPhone;
    } else if (cleanPhone.startsWith('0') && cleanPhone.length === 11) {
        cleanPhone = '91' + cleanPhone.substring(1);
    } else if (cleanPhone.startsWith('91') && cleanPhone.length > 12) {
        cleanPhone = cleanPhone.substring(0, 12);
    }
    
    if (type === 'whatsapp') {
        const whatsappUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    } else if (type === 'sms') {
        const smsUrl = `sms:${phone}?body=${encodeURIComponent(message)}`;
        window.location.href = smsUrl;
    }
}

// ==================== RESCHEDULE & CANCEL FUNCTIONS ====================

</script>
@endsection
