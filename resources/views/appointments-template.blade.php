@extends('layouts.app-new')

@section('title', 'Appointments - ClinicFlow')

@section('styles')
<style>
    /* ============================================
       APPOINTMENT-SPECIFIC STYLES
    ============================================ */
    
    /* View Tabs */
    .view-tabs {
        display: flex;
        gap: 0.25rem;
        background: var(--gray-100);
        padding: 0.25rem;
        border-radius: var(--radius-lg);
    }
    
    .view-tab {
        padding: 0.5rem 1.25rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        border: none;
        background: transparent;
        cursor: pointer;
        color: var(--gray-600);
        transition: all var(--transition-fast);
    }
    
    .view-tab.active {
        background: var(--white);
        color: var(--primary-black);
        box-shadow: var(--shadow-sm);
    }
    
    .view-tab:hover:not(.active) {
        color: var(--gray-900);
    }
    
    /* Main Layout Grid */
    .appointments-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: var(--space-lg);
        height: calc(100vh - var(--header-height) - 8rem);
    }
    
    /* Calendar Section */
    .calendar-container {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }
    
    .calendar-navigation {
        display: flex;
        align-items: center;
        gap: var(--space-md);
    }
    
    .nav-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--white);
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-md);
        color: var(--gray-700);
        cursor: pointer;
        transition: all var(--transition-fast);
    }
    
    .nav-btn:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
        color: var(--gray-900);
    }
    
    .current-date {
        font-weight: 600;
        font-size: 1rem;
        color: var(--gray-900);
        min-width: 180px;
        text-align: center;
    }
    
    /* Time Slots Grid */
    .time-slots-container {
        flex: 1;
        overflow-y: auto;
    }
    
    .time-slot-row {
        display: flex;
        border-bottom: 1px solid var(--gray-100);
        min-height: 70px;
    }
    
    .time-slot-row:last-child {
        border-bottom: none;
    }
    
    .time-label {
        width: 90px;
        padding: 1rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--gray-600);
        border-right: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        align-items: flex-start;
    }
    
    .time-slot-content {
        flex: 1;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    /* Appointment Cards */
    .appointment-card {
        background: linear-gradient(135deg, var(--info-light) 0%, #bfdbfe 100%);
        border-left: 4px solid var(--info);
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all var(--transition-base);
    }
    
    .appointment-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }
    
    .appointment-card.pending {
        background: linear-gradient(135deg, var(--warning-light) 0%, #fde68a 100%);
        border-left-color: var(--warning);
    }
    
    .appointment-card.confirmed {
        background: linear-gradient(135deg, var(--success-light) 0%, #bbf7d0 100%);
        border-left-color: var(--success);
    }
    
    .appointment-card.completed {
        background: var(--gray-100);
        border-left-color: var(--gray-400);
        opacity: 0.7;
    }
    
    .appointment-card.cancelled {
        background: var(--danger-light);
        border-left-color: var(--danger);
        opacity: 0.8;
    }
    
    .apt-patient-name {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }
    
    .apt-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    .apt-meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    /* Sidebar */
    .appointments-sidebar {
        display: flex;
        flex-direction: column;
        gap: var(--space-lg);
    }
    
    /* Upcoming Appointments Widget */
    .upcoming-widget {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    
    .widget-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        font-weight: 600;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        color: var(--gray-900);
    }
    
    .widget-body {
        max-height: 350px;
        overflow-y: auto;
    }
    
    .upcoming-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--gray-100);
        cursor: pointer;
        transition: background var(--transition-fast);
    }
    
    .upcoming-item:hover {
        background: var(--gray-50);
    }
    
    .upcoming-item:last-child {
        border-bottom: none;
    }
    
    .date-badge {
        background: linear-gradient(135deg, var(--primary-black) 0%, var(--gray-800) 100%);
        color: var(--white);
        padding: 0.75rem;
        border-radius: var(--radius-md);
        text-align: center;
        min-width: 60px;
        box-shadow: var(--shadow-sm);
    }
    
    .date-badge-day {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .date-badge-month {
        font-size: 0.6875rem;
        text-transform: uppercase;
        opacity: 0.8;
        margin-top: 0.25rem;
    }
    
    .upcoming-info {
        flex: 1;
    }
    
    .upcoming-info h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.375rem;
    }
    
    .upcoming-info .meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    /* Quick Actions Widget */
    .quick-actions {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
    }
    
    .quick-actions h5 {
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: var(--space-lg);
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        color: var(--gray-900);
    }
    
    .actions-grid {
        display: grid;
        gap: var(--space-sm);
    }
    
    /* Professional Empty State */
    .appointments-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .empty-icon-circle {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-50) 100%);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    
    .empty-icon-circle i {
        font-size: 2.5rem;
        color: var(--gray-400);
    }
    
    .appointments-empty h5 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }
    
    .appointments-empty p {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 1.5rem;
        max-width: 400px;
    }
    
    /* Modal Enhancements */
    .form-section {
        margin-bottom: var(--space-lg);
    }
    
    .form-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: var(--space-md);
        padding-bottom: var(--space-sm);
        border-bottom: 1px solid var(--gray-200);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-md);
    }
    
    /* Search Results Dropdown */
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        max-height: 250px;
        overflow-y: auto;
        z-index: 1100;
        margin-top: 0.25rem;
    }
    
    .search-result-item {
        display: flex;
        align-items: center;
        gap: var(--space-md);
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background var(--transition-fast);
        text-decoration: none;
        color: var(--gray-900);
    }
    
    .search-result-item:hover {
        background: var(--gray-50);
    }
    
    .search-result-avatar {
        width: 40px;
        height: 40px;
        background: var(--gray-200);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: var(--gray-700);
    }
    
    .search-result-info {
        flex: 1;
    }
    
    .search-result-name {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }
    
    .search-result-meta {
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .appointments-layout {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .appointments-sidebar {
            flex-direction: row;
        }
        
        .upcoming-widget,
        .quick-actions {
            flex: 1;
        }
    }
    
    @media (max-width: 768px) {
        .appointments-sidebar {
            flex-direction: column;
        }
        
        .time-label {
            width: 70px;
            font-size: 0.75rem;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-title-section">
        <h1>
            <i class="bi bi-calendar-check"></i>
            Appointments
        </h1>
        <p class="page-subtitle">Schedule and manage patient appointments</p>
    </div>
    <div class="page-actions">
        <div class="view-tabs">
            <button class="view-tab {{ $view === 'day' ? 'active' : '' }}" onclick="changeView('day')">
                Day
            </button>
            <button class="view-tab {{ $view === 'week' ? 'active' : '' }}" onclick="changeView('week')">
                Week
            </button>
            <button class="view-tab {{ $view === 'month' ? 'active' : '' }}" onclick="changeView('month')">
                Month
            </button>
        </div>
        @if(auth()->user()->role === 'reception')
        <button class="btn btn-primary" onclick="openAppointmentModal()">
            <i class="bi bi-plus-lg"></i>
            New Appointment
        </button>
        @endif
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div class="stat-icon black">
            <i class="bi bi-calendar-day"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $todayStats['total'] }}</div>
            <div class="stat-label">Today's Appointments</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $todayStats['confirmed'] }}</div>
            <div class="stat-label">Confirmed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $todayStats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="bi bi-check2-all"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $todayStats['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<!-- Main Layout -->
<div class="appointments-layout">
    <!-- Calendar Section -->
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-navigation">
                <button class="nav-btn" onclick="navigateDate(-1)">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <span class="current-date">
                    {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                </span>
                <button class="nav-btn" onclick="navigateDate(1)">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="goToToday()">
                    Today
                </button>
                @if(auth()->user()->role === 'doctor')
                <a href="{{ route('doctor.schedule') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-gear"></i>
                    Manage Schedule
                </a>
                @endif
            </div>
        </div>
        
        <div class="time-slots-container">
            @php
                $timeSlots = [];
                for ($h = 8; $h <= 20; $h++) {
                    $timeSlots[] = sprintf('%02d:00', $h);
                    $timeSlots[] = sprintf('%02d:30', $h);
                }
            @endphp
            
            @if($appointments->isEmpty())
                <div class="appointments-empty">
                    <div class="empty-icon-circle">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h5>No appointments scheduled</h5>
                    <p>There are no appointments for this day. Create a new appointment to get started.</p>
                    @if(auth()->user()->role === 'reception')
                    <button class="btn btn-primary" onclick="openAppointmentModal()">
                        <i class="bi bi-plus-lg"></i>
                        Schedule Appointment
                    </button>
                    @endif
                </div>
            @else
                @foreach($timeSlots as $slot)
                    @php
                        $slotHour = (int) substr($slot, 0, 2);
                        $slotMinute = (int) substr($slot, 3, 2);
                        $slotStart = $slotHour * 60 + $slotMinute;
                        $slotEnd = $slotStart + 30;
                        
                        $slotAppointments = $appointments->filter(function($apt) use ($slotStart, $slotEnd) {
                            $aptTime = \Carbon\Carbon::parse($apt->appointment_time);
                            $aptMinutes = $aptTime->hour * 60 + $aptTime->minute;
                            return $aptMinutes >= $slotStart && $aptMinutes < $slotEnd;
                        });
                    @endphp
                    
                    <div class="time-slot-row">
                        <div class="time-label">
                            {{ \Carbon\Carbon::parse($slot)->format('h:i A') }}
                        </div>
                        <div class="time-slot-content">
                            @foreach($slotAppointments as $apt)
                                <div class="appointment-card {{ $apt->status }}" onclick="viewAppointmentDetails({{ $apt->id }})">
                                    <div class="apt-patient-name">{{ $apt->patient->name }}</div>
                                    <div class="apt-meta">
                                        <span class="apt-meta-item">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}
                                        </span>
                                        @if($apt->patient->phone)
                                        <span class="apt-meta-item">
                                            <i class="bi bi-telephone"></i>
                                            {{ $apt->patient->phone }}
                                        </span>
                                        @endif
                                        @if($apt->reason)
                                        <span class="apt-meta-item">
                                            <i class="bi bi-file-text"></i>
                                            {{ Str::limit($apt->reason, 30) }}
                                        </span>
                                        @endif
                                        <span class="badge badge-{{ $apt->status === 'confirmed' ? 'success' : ($apt->status === 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($apt->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="appointments-sidebar">
        <!-- Upcoming Appointments -->
        <div class="upcoming-widget">
            <div class="widget-header">
                <i class="bi bi-calendar-week"></i>
                Upcoming Appointments
            </div>
            <div class="widget-body">
                @forelse($upcomingAppointments ?? [] as $apt)
                    <div class="upcoming-item" onclick="viewAppointmentDetails({{ $apt->id }})">
                        <div class="date-badge">
                            <div class="date-badge-day">{{ $apt->appointment_date->format('d') }}</div>
                            <div class="date-badge-month">{{ $apt->appointment_date->format('M') }}</div>
                        </div>
                        <div class="upcoming-info">
                            <h6>{{ $apt->patient->name }}</h6>
                            <div class="meta">
                                <span>
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}
                                </span>
                                <span class="badge badge-{{ $apt->status === 'confirmed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-state-icon" style="width: 60px; height: 60px; margin: 0 auto 1rem;">
                            <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                        </div>
                        <p style="font-size: 0.8125rem; color: var(--gray-500); margin: 0;">
                            No upcoming appointments
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h5>
                <i class="bi bi-lightning-charge"></i>
                Quick Actions
            </h5>
            <div class="actions-grid">
                @if(auth()->user()->role === 'doctor')
                <a href="{{ route('doctor.schedule') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-calendar-range"></i>
                    View Schedule
                </a>
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-speedometer2"></i>
                    Go to Dashboard
                </a>
                @else
                <a href="{{ route('reception.patients') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-people"></i>
                    View Patients
                </a>
                <a href="{{ route('reception.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-speedometer2"></i>
                    Go to Dashboard
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Appointment Modal (for creating/editing) -->
<div class="modal" id="appointmentModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Schedule Appointment</h5>
            <button class="btn-close" onclick="closeAppointmentModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="appointmentForm">
                <div class="form-section">
                    <div class="form-section-title">Patient Information</div>
                    <div class="form-group">
                        <label class="form-label">
                            Patient <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="patientSearch" placeholder="Search patient by name or phone..." autocomplete="off">
                        <input type="hidden" id="patientId" name="patient_id">
                        <div class="search-results" id="searchResults" style="display: none;"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-section-title">Appointment Details</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Date <span class="required">*</span>
                            </label>
                            <input type="date" class="form-control" name="appointment_date" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Time <span class="required">*</span>
                            </label>
                            <input type="time" class="form-control" name="appointment_time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Reason for Visit</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Enter reason for appointment..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed" selected>Confirmed</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline-secondary" onclick="closeAppointmentModal()">
                Cancel
            </button>
            <button class="btn btn-primary" onclick="submitAppointment()">
                <i class="bi bi-check-lg"></i>
                Schedule Appointment
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentDate = '{{ $date }}';
let currentView = '{{ $view }}';

// View Navigation
function changeView(view) {
    window.location.href = `?date=${currentDate}&view=${view}`;
}

function navigateDate(direction) {
    const date = new Date(currentDate);
    if (currentView === 'day') {
        date.setDate(date.getDate() + direction);
    } else if (currentView === 'week') {
        date.setDate(date.getDate() + (direction * 7));
    } else if (currentView === 'month') {
        date.setMonth(date.getMonth() + direction);
    }
    window.location.href = `?date=${date.toISOString().split('T')[0]}&view=${currentView}`;
}

function goToToday() {
    window.location.href = `?date=${new Date().toISOString().split('T')[0]}&view=${currentView}`;
}

// Modal Functions
function openAppointmentModal() {
    document.getElementById('appointmentModal').classList.add('show');
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.remove('show');
    document.getElementById('appointmentForm').reset();
}

// Patient Search
let searchTimeout;
document.getElementById('patientSearch')?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    
    if (query.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get(`/reception/search-patients?q=${encodeURIComponent(query)}`);
            const results = response.data.patients || [];
            
            if (results.length > 0) {
                const html = results.map(patient => `
                    <div class="search-result-item" onclick="selectPatient(${patient.id}, '${patient.name}')">
                        <div class="search-result-avatar">
                            ${patient.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div class="search-result-info">
                            <div class="search-result-name">${patient.name}</div>
                            <div class="search-result-meta">
                                ${patient.phone || 'No phone'} • ${patient.age || 'Age N/A'}y • ${patient.gender || 'Gender N/A'}
                            </div>
                        </div>
                    </div>
                `).join('');
                
                document.getElementById('searchResults').innerHTML = html;
                document.getElementById('searchResults').style.display = 'block';
            } else {
                document.getElementById('searchResults').style.display = 'none';
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }, 300);
});

function selectPatient(id, name) {
    document.getElementById('patientId').value = id;
    document.getElementById('patientSearch').value = name;
    document.getElementById('searchResults').style.display = 'none';
}

// Submit Appointment
async function submitAppointment() {
    const form = document.getElementById('appointmentForm');
    const formData = new FormData(form);
    formData.append('patient_id', document.getElementById('patientId').value);
    
    if (!formData.get('patient_id')) {
        showNotification('Please select a patient', 'error');
        return;
    }
    
    try {
        const response = await axios.post('{{ route("reception.appointments.store") }}', formData);
        
        if (response.data.success) {
            showNotification('Appointment scheduled successfully!', 'success');
            closeAppointmentModal();
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showNotification(error.response?.data?.message || 'Failed to schedule appointment', 'error');
    }
}

// View Appointment Details
function viewAppointmentDetails(id) {
    // Implement appointment details modal
    console.log('View appointment:', id);
}

// Close modal on backdrop click
document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAppointmentModal();
    }
});
</script>
@endsection
