@extends('layouts.app')

@section('title', 'Appointments - ClinicFlow')

@section('styles')
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    .day-header {
        background: #f8f9fa;
        padding: 0.625rem 0.5rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.7rem;
        color: #6c757d;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .day-header:last-child { border-right: none; }
    .calendar-day {
        background: #fff;
        min-height: 110px;
        padding: 0.4rem;
        cursor: pointer;
        transition: background 0.15s;
        position: relative;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
    }
    .calendar-day:nth-child(7n+7) { border-right: none; }
    .calendar-day:hover { background: #f8f9fa; }
    .calendar-day.other-month { background: #f9fafb; }
    .calendar-day.other-month .day-number { color: #ced4da; }
    .calendar-day.today .day-number-inner {
        background: #0d6efd;
        color: #fff;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .day-number {
        font-weight: 700;
        font-size: 0.85rem;
        color: #212529;
        margin-bottom: 0.25rem;
        text-align: right;
        display: flex;
        justify-content: flex-end;
    }
    .apt-chip {
        background: #dbeafe;
        color: #1e40af;
        border-left: 3px solid #3b82f6;
        padding: 0.2rem 0.35rem;
        border-radius: 3px;
        margin-bottom: 0.2rem;
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
    }
    .apt-chip.confirmed { background: #dcfce7; color: #166534; border-left-color: #22c55e; }
    .apt-chip.completed { background: #f3f4f6; color: #374151; border-left-color: #9ca3af; }
    .apt-chip.cancelled { background: #fee2e2; color: #991b1b; border-left-color: #ef4444; }
    .more-chip { font-size:0.62rem; color:#6b7280; font-weight:600; padding:0.15rem 0.3rem; cursor:pointer; text-align:center; }
    .more-chip:hover { color:#1f2937; text-decoration:underline; }
    .apt-detail-row { display:flex; align-items:flex-start; gap:0.75rem; padding:0.875rem 1rem; border-bottom:1px solid #f0f0f0; }
    .apt-detail-row:last-child { border-bottom:none; }
    .apt-time-badge { background:#f1f5f9; color:#1e293b; font-weight:700; font-size:0.75rem; padding:0.35rem 0.6rem; border-radius:6px; min-width:70px; text-align:center; flex-shrink:0; }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Appointments</h4>
        <p class="text-muted mb-0 mt-1 fs-7">Schedule and manage patient appointments</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white border rounded p-1 d-flex shadow-sm">
            <button class="btn btn-sm px-3 btn-link text-decoration-none text-muted"
                onclick="window.location.href='{{ route('doctor.appointments') }}?view=day'">Day</button>
            <button class="btn btn-sm px-3 btn-link text-decoration-none text-muted"
                onclick="window.location.href='{{ route('doctor.appointments') }}?view=week'">Week</button>
            <button class="btn btn-sm px-3 btn-link text-decoration-none text-muted"
                onclick="window.location.href='{{ route('doctor.appointments') }}?view=month'">Month</button>
            <button class="btn btn-sm px-3 btn-light border shadow-sm fw-bold">
                <i class="bi bi-grid-3x3-gap me-1"></i>Calendar</button>
        </div>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="bi bi-plus-lg me-1"></i> New Appointment
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-dark text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-calendar3 fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['total'] ?? 0 }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Today's Total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['confirmed'] ?? 0 }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Confirmed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['pending'] ?? 0 }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="text-white rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#6f42c1;">
                    <i class="bi bi-check-all fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $todayStats['completed'] ?? 0 }}</h3>
                    <div class="text-muted fs-7 text-uppercase fw-semibold mt-1">Completed</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                    <span class="fs-6 fw-bold text-dark px-2" id="currentMonthYear" style="min-width:160px;text-align:center;"></span>
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary px-3" onclick="goToToday()">Today</button>
                    <button class="btn btn-sm btn-outline-secondary px-3" onclick="openShareModal()">
                        <i class="bi bi-share me-1"></i>Share
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="calendar-grid" id="calendarGrid">
                    <div class="day-header">Sun</div>
                    <div class="day-header">Mon</div>
                    <div class="day-header">Tue</div>
                    <div class="day-header">Wed</div>
                    <div class="day-header">Thu</div>
                    <div class="day-header">Fri</div>
                    <div class="day-header">Sat</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming Appointments</h6>
            </div>
            <div class="card-body p-0" style="max-height:420px;overflow-y:auto;">
                <div class="list-group list-group-flush">
                    @forelse($upcomingAppointments as $apt)
                    @php $sc = $apt->status === 'confirmed' ? 'success' : ($apt->status === 'scheduled' ? 'primary' : 'secondary'); @endphp
                    <button type="button" class="list-group-item list-group-item-action p-3"
                        onclick="showDayDetail('{{ \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d') }}')">
                        <div class="d-flex align-items-start gap-2">
                            <div class="bg-dark text-white rounded text-center d-flex flex-column justify-content-center"
                                style="width:40px;height:40px;flex-shrink:0;">
                                <span class="fw-bold lh-1" style="font-size:0.85rem;">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d') }}</span>
                                <span class="text-uppercase" style="font-size:0.55rem;">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M') }}</span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size:0.8rem;">{{ $apt->patient->name ?? 'Unknown' }}</h6>
                                <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                    <small class="text-muted" style="font-size:0.7rem;"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</small>
                                    <small class="text-{{ $sc }} fw-semibold text-capitalize" style="font-size:0.7rem;">· {{ $apt->status }}</small>
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

{{-- Day Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-check me-2 text-primary"></i><span id="modalDateTitle">Appointments</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="modalBody" style="max-height:70vh;overflow-y:auto;">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- Reschedule Modal --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i>Reschedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-7"><i class="bi bi-calendar3 me-1 text-muted"></i> New Date</label>
                    <input type="date" id="rescheduleDate" class="form-control" min="{{ date('Y-m-d') }}" onchange="fetchRescheduleSlots()">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold text-dark fs-7"><i class="bi bi-clock me-1 text-muted"></i> New Time</label>
                    <select id="rescheduleTime" class="form-select" disabled>
                        <option value="">Select Date First</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-medium" id="btnSubmitReschedule" onclick="submitReschedule()">Confirm</button>
            </div>
        </div>
    </div>
</div>

{{-- Share Modal --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-share me-2 text-primary"></i>Share Calendar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3">Share this calendar view with others:</p>
                <div class="input-group mb-4">
                    <input type="text" id="calendarLink" class="form-control bg-light" readonly>
                    <button class="btn btn-primary" onclick="copyCalendarLink()"><i class="bi bi-clipboard"></i> Copy</button>
                </div>
                <div class="row g-2">
                    <div class="col-6"><button class="btn btn-warning text-white w-100 fw-bold" onclick="shareViaEmail()"><i class="bi bi-envelope-fill me-1"></i>Email</button></div>
                    <div class="col-6"><button class="btn btn-success w-100 fw-bold" onclick="shareViaWhatsApp()"><i class="bi bi-whatsapp me-1"></i>WhatsApp</button></div>
                    <div class="col-6"><button class="btn btn-danger w-100 fw-bold" onclick="printCalendar()"><i class="bi bi-printer-fill me-1"></i>Print</button></div>
                    <div class="col-6"><button class="btn btn-secondary w-100 fw-bold" onclick="downloadCalendarPDF()"><i class="bi bi-file-pdf-fill me-1"></i>PDF</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- New Appointment Modal --}}
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-calendar-plus me-2 text-primary"></i>New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('doctor.appointments.store') }}" method="POST" id="calNewAptForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-dark fs-7">Patient <span class="text-danger">*</span></label>
                        <input type="text" id="patientSearchInput2" class="form-control" placeholder="Type patient name..." autocomplete="off">
                        <input type="hidden" name="patient_id" id="patientIdHidden2" required>
                        <div id="patientSuggestions2" class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:9999;top:100%;display:none;max-height:200px;overflow-y:auto;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">Date <span class="text-danger">*</span></label>
                        <input type="date" name="appointment_date" id="newAptDate2" class="form-control"
                            min="{{ date('Y-m-d') }}" required onchange="fetchSlots2()">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">Time <span class="text-danger">*</span></label>
                        <select name="appointment_time" id="newAptTime2" class="form-select" required disabled>
                            <option value="">Select Date First</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="Reason for visit (optional)">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium" id="btnCalNewApt">
                        <i class="bi bi-plus-lg me-1"></i>Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentDate = new Date();
    let appointments = @json($appointments);
    let selectedRescheduleId = null;
    let detailModal, rescheduleModal, shareModal;

    document.addEventListener('DOMContentLoaded', function () {
        detailModal     = new bootstrap.Modal(document.getElementById('detailModal'));
        rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
        shareModal      = new bootstrap.Modal(document.getElementById('shareModal'));
        renderCalendar();
        initPatientSearch();

        // AJAX submit for new appointment
        const calForm = document.getElementById('calNewAptForm');
        if (calForm) {
            calForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const patientId = document.getElementById('patientIdHidden2').value;
                const date = document.getElementById('newAptDate2').value;
                const time = document.getElementById('newAptTime2').value;
                if (!patientId) { showToast('Please select a patient from the list.', 'error'); return; }
                if (!date)      { showToast('Please select a date.', 'error'); return; }
                if (!time)      { showToast('Please select a time slot.', 'error'); return; }
                const btn = document.getElementById('btnCalNewApt');
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';
                btn.disabled = true;
                axios.post(calForm.action, new FormData(calForm))
                    .then(res => {
                        if (res.data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('newAppointmentModal')).hide();
                            showToast('Appointment scheduled successfully!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(res.data.message || 'Failed to create appointment.', 'error');
                            btn.innerHTML = orig; btn.disabled = false;
                        }
                    })
                    .catch(err => {
                        showToast(err.response?.data?.message || 'An error occurred.', 'error');
                        btn.innerHTML = orig; btn.disabled = false;
                    });
            });
        }

        document.getElementById('newAppointmentModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('patientSearchInput2').value = '';
            document.getElementById('patientIdHidden2').value = '';
            document.getElementById('patientSuggestions2').style.display = 'none';
            document.getElementById('newAptDate2').value = '';
            const ts = document.getElementById('newAptTime2');
            ts.innerHTML = '<option value="">Select Date First</option>';
            ts.disabled = true;
        });
    });

    // ---- CALENDAR RENDER ----
    function renderCalendar() {
        const year  = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const mNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('currentMonthYear').textContent = mNames[month] + ' ' + year;

        const firstDay    = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevLast    = new Date(year, month, 0).getDate();
        const today       = new Date();
        let html = '', dc = 1, nd = 1;

        for (let i = 0; i < 42; i++) {
            let dayNum, isCur, cellDate;
            if (i < firstDay) {
                dayNum = prevLast - firstDay + i + 1; isCur = false;
                cellDate = new Date(year, month - 1, dayNum);
            } else if (dc <= daysInMonth) {
                dayNum = dc++; isCur = true;
                cellDate = new Date(year, month, dayNum);
            } else {
                dayNum = nd++; isCur = false;
                cellDate = new Date(year, month + 1, dayNum);
            }
            const isToday = isCur && dayNum === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const dateStr = cellDate.toISOString().split('T')[0];
            const dayApts = appointments.filter(a => (a.appointment_date||'').split('T')[0].split(' ')[0] === dateStr);
            const cls = ['calendar-day', !isCur ? 'other-month' : '', isToday ? 'today' : ''].filter(Boolean).join(' ');

            html += `<div class="${cls}" onclick="showDayDetail('${dateStr}')">`;
            html += `<div class="day-number"><span class="day-number-inner">${dayNum}</span></div>`;
            dayApts.slice(0, 3).forEach(a => {
                const sc = a.status || 'scheduled';
                const t  = (a.appointment_time||'').substring(0, 5);
                const nm = (a.patient && a.patient.name) ? a.patient.name : 'Patient';
                html += `<div class="apt-chip ${sc}" onclick="event.stopPropagation();showDayDetail('${dateStr}')">${t} ${nm}</div>`;
            });
            if (dayApts.length > 3) html += `<div class="more-chip">+${dayApts.length - 3} more</div>`;
            html += `</div>`;
        }

        const grid = document.getElementById('calendarGrid');
        grid.querySelectorAll('.calendar-day').forEach(el => el.remove());
        grid.insertAdjacentHTML('beforeend', html);
    }

    function goToToday() { currentDate = new Date(); renderCalendar(); }

    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        const y = currentDate.getFullYear(), m = currentDate.getMonth() + 1;
        fetch(`{{ route('doctor.appointments') }}?year=${y}&month=${m}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => { if (data.appointments) appointments = data.appointments; renderCalendar(); })
        .catch(() => renderCalendar());
    }

    // ---- DAY DETAIL ----
    function showDayDetail(dateStr) {
        const mNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const d = new Date(dateStr + 'T00:00:00');
        document.getElementById('modalDateTitle').textContent = mNames[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        detailModal.show();

        const dayApts = appointments.filter(a => (a.appointment_date||'').split('T')[0].split(' ')[0] === dateStr);
        dayApts.sort((a, b) => (a.appointment_time||'').localeCompare(b.appointment_time||''));
        const body = document.getElementById('modalBody');

        if (dayApts.length === 0) {
            body.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 opacity-50 d-block mb-3"></i><p class="mb-0">No appointments scheduled for this day.</p></div>`;
            return;
        }

        const scMap = { scheduled:'primary', confirmed:'success', completed:'secondary', cancelled:'danger', no_show:'warning' };
        let html = '';
        dayApts.forEach(a => {
            const sc   = a.status || 'scheduled';
            const scTx = sc.charAt(0).toUpperCase() + sc.slice(1);
            const badge = scMap[sc] || 'secondary';
            const time  = (a.appointment_time||'--:--').substring(0, 5);
            const name  = (a.patient && a.patient.name) ? a.patient.name : 'Unknown Patient';
            const phone = a.patient && a.patient.phone ? a.patient.phone : null;
            const reason = a.reason || null;
            const canAct = sc !== 'cancelled' && sc !== 'completed';
            html += `<div class="apt-detail-row">
                <div class="apt-time-badge">${fmt12(time)}</div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="mb-0 fw-bold text-dark">${name}</h6>
                        <span class="badge bg-${badge} bg-opacity-10 text-${badge} border border-${badge} border-opacity-25 rounded-pill fs-8 px-2 py-1 text-uppercase ms-2">${scTx}</span>
                    </div>
                    ${phone ? `<div class="text-muted" style="font-size:.75rem;"><i class="bi bi-telephone me-1"></i>${phone}</div>` : ''}
                    ${reason ? `<div class="text-muted mt-1" style="font-size:.75rem;"><i class="bi bi-chat-left-text me-1"></i>${reason}</div>` : ''}
                    ${canAct ? `<div class="d-flex gap-2 mt-2">
                        <button class="btn btn-sm btn-outline-primary px-3" onclick="startReschedule(${a.id})"><i class="bi bi-calendar-event me-1"></i>Reschedule</button>
                        <button class="btn btn-sm btn-outline-danger px-3" onclick="cancelAppointment(${a.id})"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                    </div>` : ''}
                </div>
            </div>`;
        });
        body.innerHTML = html;
    }

    function fmt12(time) {
        if (!time || time === '--:--') return time;
        try {
            const [h, m] = time.split(':').map(Number);
            return (h % 12 || 12) + ':' + String(m).padStart(2,'0') + ' ' + (h >= 12 ? 'PM' : 'AM');
        } catch(e) { return time; }
    }

    // ---- RESCHEDULE ----
    function startReschedule(id) {
        selectedRescheduleId = id;
        document.getElementById('rescheduleDate').value = '';
        const sel = document.getElementById('rescheduleTime');
        sel.innerHTML = '<option value="">Select Date First</option>';
        sel.disabled = true;
        detailModal.hide();
        setTimeout(() => rescheduleModal.show(), 350);
    }

    function fetchRescheduleSlots() {
        const date = document.getElementById('rescheduleDate').value;
        const sel  = document.getElementById('rescheduleTime');
        if (!date) { sel.innerHTML = '<option value="">Select Date First</option>'; sel.disabled = true; return; }
        sel.innerHTML = '<option value="">Loading...</option>'; sel.disabled = true;
        const excl = selectedRescheduleId ? `&exclude_id=${selectedRescheduleId}` : '';
        axios.get(`{{ route('doctor.appointments.slots') }}?date=${date}${excl}`)
            .then(res => {
                sel.innerHTML = '<option value="">Select Time</option>';
                if (res.data.slots && res.data.slots.length > 0) {
                    res.data.slots.forEach(s => sel.innerHTML += `<option value="${s}">${fmt12(s)}</option>`);
                    sel.disabled = false;
                } else {
                    sel.innerHTML = `<option value="">${res.data.message || 'No slots available'}</option>`;
                }
            })
            .catch(() => { sel.innerHTML = '<option value="">Error loading slots</option>'; });
    }

    function submitReschedule() {
        if (!selectedRescheduleId) return;
        const date = document.getElementById('rescheduleDate').value;
        const time = document.getElementById('rescheduleTime').value;
        if (!date || !time) { alert('Please select both date and time.'); return; }
        const btn = document.getElementById('btnSubmitReschedule');
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Wait...';
        btn.disabled = true;
        axios.put(`/doctor/appointments/${selectedRescheduleId}/reschedule`, { appointment_date: date, appointment_time: time })
            .then(res => {
                if (res.data.success) {
                    rescheduleModal.hide();
                    showToast('Appointment rescheduled!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(res.data.message || 'Failed to reschedule.', 'error');
                    btn.innerHTML = orig; btn.disabled = false;
                }
            })
            .catch(err => {
                const msg = err.response?.data?.message || 'An error occurred.';
                showToast(msg, 'error');
                btn.innerHTML = orig; btn.disabled = false;
            });
    }

    // ---- CANCEL ----
    function cancelAppointment(id) {
        if (!confirm('Cancel this appointment?')) return;
        fetch(`/doctor/appointments/${id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ status: 'cancelled' })
        }).then(r => r.json()).then(data => {
            if (data.success) { detailModal.hide(); showToast('Appointment cancelled.', 'success'); setTimeout(() => location.reload(), 1500); }
            else showToast(data.message || 'Failed.', 'error');
        }).catch(() => showToast('An error occurred.', 'error'));
    }

    // ---- SHARE ----
    function openShareModal() {
        const y = currentDate.getFullYear(), m = currentDate.getMonth() + 1;
        document.getElementById('calendarLink').value = window.location.origin + '{{ route('doctor.appointments.calendar') }}?year=' + y + '&month=' + m;
        shareModal.show();
    }
    function copyCalendarLink() {
        navigator.clipboard.writeText(document.getElementById('calendarLink').value)
            .then(() => showToast('Link copied!', 'success'))
            .catch(() => showToast('Could not copy.', 'error'));
    }
    function shareViaEmail() {
        const m = currentDate.toLocaleString('default',{month:'long'}), y = currentDate.getFullYear();
        const u = document.getElementById('calendarLink').value;
        window.location.href = `mailto:?subject=${encodeURIComponent('ClinicFlow - '+m+' '+y)}&body=${encodeURIComponent(u)}`;
    }
    function shareViaWhatsApp() {
        const u = document.getElementById('calendarLink').value;
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(u)}`, '_blank');
    }
    function downloadCalendarPDF() { showToast('PDF coming soon!', 'info'); }
    function printCalendar() { window.print(); }

    // ---- NEW APPOINTMENT SLOTS ----
    function fetchSlots2() {
        const date = document.getElementById('newAptDate2').value;
        const sel  = document.getElementById('newAptTime2');
        if (!date) { sel.innerHTML = '<option value="">Select Date First</option>'; sel.disabled = true; return; }
        sel.innerHTML = '<option value="">Loading...</option>'; sel.disabled = true;
        axios.get(`{{ route('doctor.appointments.slots') }}?date=${date}`)
            .then(res => {
                sel.innerHTML = '<option value="">Select Time</option>';
                if (res.data.slots && res.data.slots.length > 0) {
                    res.data.slots.forEach(s => sel.innerHTML += `<option value="${s}">${fmt12(s)}</option>`);
                    sel.disabled = false;
                } else {
                    sel.innerHTML = `<option value="">${res.data.message || 'No slots available'}</option>`;
                }
            })
            .catch(() => { sel.innerHTML = '<option value="">Error loading slots</option>'; });
    }

    function initPatientSearch() {
        const input = document.getElementById('patientSearchInput2');
        const hidden = document.getElementById('patientIdHidden2');
        const box = document.getElementById('patientSuggestions2');
        if (!input) return;
        let timer;
        input.addEventListener('input', function () {
            const q = this.value.trim(); hidden.value = '';
            if (q.length < 2) { box.style.display = 'none'; return; }
            clearTimeout(timer);
            timer = setTimeout(() => {
                axios.get(`{{ route('doctor.patients.search') }}?query=${encodeURIComponent(q)}`)
                    .then(res => {
                        box.innerHTML = '';
                        const pts = res.data.patients || [];
                        if (!pts.length) { box.innerHTML = '<div class="list-group-item text-muted">No patients found</div>'; }
                        else pts.forEach(p => {
                            const el = document.createElement('button');
                            el.type = 'button';
                            el.className = 'list-group-item list-group-item-action d-flex justify-content-between';
                            el.innerHTML = `<span class="fw-semibold">${p.name}</span><span class="text-muted small">${p.phone||''}</span>`;
                            el.addEventListener('click', () => { input.value = p.name; hidden.value = p.id; box.style.display = 'none'; });
                            box.appendChild(el);
                        });
                        box.style.display = 'block';
                    })
                    .catch(() => { box.style.display = 'none'; });
            }, 300);
        });
        document.addEventListener('click', e => { if (!input.contains(e.target) && !box.contains(e.target)) box.style.display = 'none'; });
    }

    // ---- TOAST ----
    function showToast(message, type) {
        const col = { success:'#22c55e', error:'#ef4444', info:'#3b82f6' };
        const ico = { success:'check-circle-fill', error:'exclamation-circle-fill', info:'info-circle-fill' };
        const el = document.createElement('div');
        el.style.cssText = `position:fixed;top:20px;right:20px;padding:.875rem 1.25rem;background:${col[type]||col.info};color:#fff;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,.18);z-index:99999;font-weight:600;font-size:.875rem;display:flex;align-items:center;gap:.5rem;`;
        el.innerHTML = `<i class="bi bi-${ico[type]||ico.info}"></i> ${message}`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(), 300); }, 3000);
    }
</script>
@endsection
