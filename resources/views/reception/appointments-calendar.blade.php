@extends('layouts.app')

@section('title', 'Appointments - ClinicFlow')

@section('styles')
<style>
    /* Page Header */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
    
    /* View Tabs */
    .view-tabs { display: flex; gap: 0.5rem; background: var(--color-gray-100); padding: 0.25rem; border-radius: 12px; border: 2px solid #e5e7eb; }
    .view-tab { padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.8rem; font-weight: 700; border: 2px solid transparent; background: transparent; cursor: pointer; color: var(--color-gray-600); transition: all 0.2s ease; }
    .view-tab.active { background: var(--color-white); color: var(--color-black); box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-color: #d1d5db; }
    .view-tab:hover:not(.active) { background: rgba(255,255,255,0.6); }
    
    /* Stats Row */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-box { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-box:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.12); transform: translateY(-3px); border-color: #d1d5db; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .stat-icon.today { background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); color: #fff; }
    .stat-icon.confirmed { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff; }
    .stat-icon.pending { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; }
    .stat-icon.completed { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: #fff; }
    .stat-info { flex: 1; }
    .stat-info .value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: #1a1a1a; }
    .stat-info .label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 500; }
    
    /* Calendar Container */
    .calendar-container { background: #ffffff; border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    
    /* Calendar Header */
    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb; }
    .calendar-nav { display: flex; align-items: center; gap: 1rem; }
    .calendar-nav button { background: #ffffff; border: 2px solid #d1d5db; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease; }
    .calendar-nav button:hover { background: #f9fafb; border-color: #1a1a1a; }
    .calendar-nav .month-year { font-size: 1.25rem; font-weight: 700; min-width: 200px; text-align: center; color: #1f2937; }
    
    /* Calendar Grid - Side by Side Layout like 3rd photo */
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; }
    
    /* Day Headers */
    .day-header { background: #f3f4f6; padding: 0.75rem; text-align: center; font-weight: 700; font-size: 0.875rem; color: #374151; border-right: 1px solid #d1d5db; border-bottom: 1px solid #d1d5db; text-transform: uppercase; letter-spacing: 0.5px; }
    .day-header:last-child { border-right: none; }
    
    /* Calendar Day Cells - Side by Side with proper borders */
    .calendar-day { background: #ffffff; min-height: 120px; padding: 0.5rem; cursor: pointer; transition: all 0.2s ease; position: relative; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; display: flex; flex-direction: column; }
    .calendar-day:nth-child(7n+7) { border-right: none; }
    .calendar-day:hover { background: #fafbfc; }
    .calendar-day.other-month { background: #f9fafb; }
    .calendar-day.other-month .day-number { color: #9ca3af; }
    .calendar-day.today .day-number { background: #fbbf24; color: #ffffff; }
    
    .day-number { font-weight: 700; font-size: 1rem; color: #1f2937; margin-bottom: 0.5rem; text-align: right; padding: 0.25rem 0.4rem; display: inline-block; align-self: flex-end; border-radius: 4px; }
    
    /* Appointment Boxes - Like 3rd Photo with colored boxes */
    .appointment-box { background: #a7f3d0; color: #065f46; padding: 0.4rem 0.5rem; border-radius: 4px; margin-bottom: 0.3rem; font-size: 0.7rem; font-weight: 600; line-height: 1.3; cursor: pointer; transition: all 0.2s ease; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; border-left: 3px solid #10b981; }
    .appointment-box:hover { transform: scale(1.02); box-shadow: 0 2px 8px rgba(16,185,129,0.3); }
    .appointment-box.scheduled { background: #bfdbfe; color: #1e40af; border-left-color: #3b82f6; }
    .appointment-box.confirmed { background: #a7f3d0; color: #065f46; border-left-color: #10b981; }
    .appointment-box.completed { background: #e5e7eb; color: #374151; border-left-color: #6b7280; opacity: 0.8; }
    .appointment-box.cancelled { background: #fecaca; color: #991b1b; border-left-color: #ef4444; opacity: 0.7; }
    
    .more-count { font-size: 0.65rem; color: #6b7280; font-weight: 600; text-align: center; padding: 0.2rem; cursor: pointer; }
    .more-count:hover { color: #1f2937; }
    
    /* Detail Modal */
    .detail-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 4rem 1rem; }
    .detail-modal.show { display: flex; }
    
    .detail-modal-content { background: #ffffff; border-radius: 16px; width: 100%; max-width: 550px; max-height: 100%; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 3px solid #e5e7eb; margin: auto; }
    
    .detail-modal-header { flex-shrink: 0; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #ffffff; padding: 1.5rem; border-radius: 13px 13px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .detail-modal-header h5 { font-size: 1.25rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
    .detail-modal-header .close-btn { background: rgba(255,255,255,0.2); border: none; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 1.5rem; transition: all 0.2s ease; }
    .detail-modal-header .close-btn:hover { background: rgba(255,255,255,0.35); transform: rotate(90deg); }
    
    .detail-modal-body { flex: 1; overflow-y: auto; padding: 1.5rem; }
    
    .appointment-detail { background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border: 2px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; transition: all 0.2s ease; }
    .appointment-detail:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #d1d5db; }
    
    .appointment-detail-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb; }
    .appointment-detail-header .patient-info h6 { font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0 0 0.25rem; }
    .appointment-detail-header .time-info { font-size: 0.875rem; color: #6b7280; font-weight: 600; }
    
    .status-badge { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-badge.scheduled { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    .status-badge.confirmed { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-badge.completed { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    
    .appointment-detail-body { display: flex; flex-direction: column; gap: 0.5rem; }
    .detail-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #4b5563; }
    .detail-row i { width: 20px; color: #1f2937; }
    .detail-row strong { color: #1f2937; }
    
    .appointment-actions { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e5e7eb; }
    .action-btn { flex: 1; padding: 0.75rem; border: 2px solid; border-radius: 10px; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .action-btn.approve { background: #dcfce7; color: #166534; border-color: #86efac; }
    .action-btn.approve:hover { background: #bbf7d0; box-shadow: 0 4px 12px rgba(34,197,94,0.3); }
    .action-btn.reschedule { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
    .action-btn.reschedule:hover { background: #bfdbfe; box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
    .action-btn.cancel { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
    .action-btn.cancel:hover { background: #fecaca; box-shadow: 0 4px 12px rgba(239,68,68,0.3); }
    
    .empty-state { text-align: center; padding: 2rem; color: #9ca3af; }
    .empty-state i { font-size: 2rem; margin-bottom: 0.5rem; }
    
    /* Loading State */
    .loading { text-align: center; padding: 2rem; color: #6b7280; }
    
    /* Animations */
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    /* Main Grid Layout */
    .main-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; }
    
    /* Side Panel */
    .side-panel { display: flex; flex-direction: column; gap: 1rem; }
    .upcoming-card { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .upcoming-card .header { padding: 0.875rem 1rem; border-bottom: 1px solid var(--color-gray-200); font-weight: 600; font-size: 0.875rem; background: var(--color-gray-50); }
    .upcoming-list { max-height: 300px; overflow-y: auto; }
    .upcoming-item { display: flex; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-gray-100); cursor: pointer; transition: background var(--transition-fast); }
    .upcoming-item:hover { background: var(--color-gray-50); }
    .upcoming-item .date-box { background: var(--color-black); color: var(--color-white); padding: 0.5rem; border-radius: 8px; text-align: center; min-width: 48px; }
    .upcoming-item .date-box .day { font-size: 1rem; font-weight: 700; line-height: 1; }
    .upcoming-item .date-box .month { font-size: 0.6rem; text-transform: uppercase; }
    .upcoming-item .info h6 { font-size: 0.8rem; font-weight: 600; margin: 0 0 0.25rem; }
    .upcoming-item .info small { font-size: 0.7rem; color: var(--color-gray-500); }
    
    .quick-actions { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .quick-actions h5 { font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .action-item { padding: 0.625rem 0.75rem; border-bottom: 1px solid var(--color-gray-100); display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-decoration: none; color: inherit; transition: background 0.15s ease; border-radius: 8px; margin-bottom: 0.5rem; }
    .action-item:hover { background: var(--color-gray-50); }
    .action-icon { width: 32px; height: 32px; background: var(--color-gray-100); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; border-radius: 8px; }
    .action-text h6 { font-size: 0.75rem; font-weight: 600; margin: 0; }
    .action-text small { font-size: 0.65rem; color: var(--color-gray-500); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar-check"></i> Appointments</h1>
        <p class="text-muted mb-0" style="font-size: 0.8rem;">Schedule and manage patient appointments</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="view-tabs">
            <button class="view-tab" onclick="window.location.href='{{ route('reception.appointments') }}'">Day</button>
            <button class="view-tab" onclick="window.location.href='{{ route('reception.appointments') }}?view=week'">Week</button>
            <button class="view-tab" onclick="window.location.href='{{ route('reception.appointments') }}?view=month'">Month</button>
            <button class="view-tab active"><i class="bi bi-grid-3x3-gap"></i> Calendar</button>
        </div>
        <button class="btn btn-dark" onclick="window.location.href='{{ route('reception.appointments') }}'">
            <i class="bi bi-plus-lg"></i> New Appointment
        </button>
    </div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon today"><i class="bi bi-calendar-day"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['total'] ?? 0 }}</div>
            <div class="label">Today's Appointments</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon confirmed"><i class="bi bi-check-circle"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['confirmed'] ?? 0 }}</div>
            <div class="label">Confirmed</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon pending"><i class="bi bi-clock-history"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['pending'] ?? 0 }}</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon completed"><i class="bi bi-check-all"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['completed'] ?? 0 }}</div>
            <div class="label">Completed</div>
        </div>
    </div>
</div>

<div class="main-grid">
    <!-- Calendar Section -->
    <div class="calendar-section">
        <div class="calendar-container">
            <!-- Calendar Header -->
            <div class="calendar-header">
                <div class="calendar-nav">
                    <button id="prevMonth" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <div class="month-year" id="currentMonthYear"></div>
                    <button id="nextMonth" onclick="changeMonth(1)">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="shareCalendar()" style="background: #ffffff; border: 2px solid #d1d5db; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-share"></i> Share
                    </button>
                </div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="calendar-grid" id="calendarGrid">
                <!-- Day Headers -->
                <div class="day-header">SUN</div>
                <div class="day-header">MON</div>
                <div class="day-header">TUE</div>
                <div class="day-header">WED</div>
                <div class="day-header">THU</div>
                <div class="day-header">FRI</div>
                <div class="day-header">SAT</div>
                
                <!-- Calendar Days will be populated by JavaScript as direct children -->
            </div>
        </div>
    </div>

    <!-- Side Panel -->
    <div class="side-panel">
        <!-- Upcoming Appointments -->
        <div class="upcoming-card">
            <div class="header">
                <i class="bi bi-clock-history"></i> Upcoming Appointments
            </div>
            <div class="upcoming-list" id="upcomingList">
                @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
                    @foreach($upcomingAppointments as $apt)
                    <div class="upcoming-item" onclick="showDayDetail('{{ $apt->appointment_date }}')">
                        <div class="date-box">
                            <div class="day">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d') }}</div>
                            <div class="month">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M') }}</div>
                        </div>
                        <div class="info">
                            <h6>{{ $apt->patient->name ?? 'Unknown' }}</h6>
                            <small>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }} - Dr. {{ $apt->doctor->name ?? 'Unknown' }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                        <i class="bi bi-calendar-x"></i><br>No upcoming appointments
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h5><i class="bi bi-lightning"></i> Quick Actions</h5>
            <a href="{{ route('reception.appointments') }}" class="action-item">
                <div class="action-icon">
                    <i class="bi bi-plus"></i>
                </div>
                <div class="action-text">
                    <h6>Book Appointment</h6>
                    <small>Schedule new appointment</small>
                </div>
            </a>
            <a href="{{ route('reception.patients') }}" class="action-item">
                <div class="action-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="action-text">
                    <h6>Add Patient</h6>
                    <small>Register new patient</small>
                </div>
            </a>
            <a href="{{ route('reception.appointments') }}?status=pending" class="action-item">
                <div class="action-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="action-text">
                    <h6>Pending Approvals</h6>
                    <small>View pending requests</small>
                </div>
            </a>
            <a href="{{ route('reception.dashboard') }}" class="action-item">
                <div class="action-icon">
                    <i class="bi bi-grid"></i>
                </div>
                <div class="action-text">
                    <h6>Dashboard</h6>
                    <small>Back to overview</small>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="detail-modal" id="detailModal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h5><i class="fas fa-calendar-check"></i> <span id="modalDate"></span></h5>
            <button class="close-btn" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="detail-modal-body" id="modalBody">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="detail-modal" id="rescheduleModal">
    <div class="detail-modal-content" style="max-width: 500px;">
        <div class="detail-modal-header">
            <h5><i class="fas fa-calendar-alt"></i> Reschedule Appointment</h5>
            <button class="close-btn" onclick="closeRescheduleModal()">&times;</button>
        </div>
        <div class="detail-modal-body" style="padding: 1.5rem;">
            <div class="reschedule-form">
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                        <i class="fas fa-calendar"></i> New Date
                    </label>
                    <input type="date" id="rescheduleDate" class="form-control" 
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem;"
                           min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                        <i class="fas fa-clock"></i> New Time
                    </label>
                    <input type="time" id="rescheduleTime" class="form-control"
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div class="form-actions" style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button onclick="closeRescheduleModal()" 
                            style="padding: 0.75rem 1.5rem; border: 2px solid #e5e7eb; background: white; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button onclick="submitReschedule()" 
                            style="padding: 0.75rem 1.5rem; border: none; background: #3b82f6; color: white; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-check"></i> Confirm Reschedule
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="detail-modal" id="shareModal">
    <div class="detail-modal-content" style="max-width: 500px;">
        <div class="detail-modal-header">
            <h5><i class="bi bi-share"></i> Share Calendar</h5>
            <button class="close-btn" onclick="closeShareModal()">&times;</button>
        </div>
        <div class="detail-modal-body" style="padding: 1.5rem;">
            <p style="margin-bottom: 1.5rem; color: #6b7280; font-size: 0.9rem;">Share this calendar view with others:</p>
            
            <div style="display: grid; gap: 1rem;">
                <!-- Copy Link -->
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; border: 2px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="font-weight: 600; color: #374151; font-size: 0.875rem;">
                            <i class="bi bi-link-45deg"></i> Calendar Link
                        </label>
                        <button onclick="copyCalendarLink()" 
                                style="padding: 0.4rem 0.8rem; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <input type="text" id="calendarLink" readonly 
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.8rem; background: white; color: #6b7280;"
                           value="">
                </div>
                
                <!-- Share Options -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                    <button onclick="shareViaEmail()" 
                            style="padding: 0.875rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-envelope-fill" style="font-size: 1.1rem;"></i>
                        Email
                    </button>
                    
                    <button onclick="shareViaWhatsApp()" 
                            style="padding: 0.875rem; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-whatsapp" style="font-size: 1.1rem;"></i>
                        WhatsApp
                    </button>
                    
                    <button onclick="downloadCalendarPDF()" 
                            style="padding: 0.875rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-file-pdf-fill" style="font-size: 1.1rem;"></i>
                        PDF
                    </button>
                    
                    <button onclick="printCalendar()" 
                            style="padding: 0.875rem; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-printer-fill" style="font-size: 1.1rem;"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Calendar Script Version: 2.0.1 - Fixed reschedule functionality
console.log('Calendar script loaded - Version 2.0.1');

let currentDate = new Date();
let appointments = @json($appointments);

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month/year display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('currentMonthYear').textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startDayOfWeek = firstDay.getDay(); // 0 = Sunday
    
    // Get previous month's last days
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    
    let html = '';
    let dayCounter = 1;
    let nextMonthDay = 1;
    
    // Calculate total cells needed (6 rows)
    const totalCells = 42;
    
    for (let i = 0; i < totalCells; i++) {
        let dayNumber, isCurrentMonth, cellDate;
        
        if (i < startDayOfWeek) {
            // Previous month days
            dayNumber = prevMonthLastDay - startDayOfWeek + i + 1;
            isCurrentMonth = false;
            cellDate = new Date(year, month - 1, dayNumber);
        } else if (dayCounter <= daysInMonth) {
            // Current month days
            dayNumber = dayCounter;
            isCurrentMonth = true;
            cellDate = new Date(year, month, dayNumber);
            dayCounter++;
        } else {
            // Next month days
            dayNumber = nextMonthDay;
            isCurrentMonth = false;
            cellDate = new Date(year, month + 1, nextMonthDay);
            nextMonthDay++;
        }
        
        const isToday = isCurrentMonth && 
                       dayNumber === new Date().getDate() && 
                       month === new Date().getMonth() && 
                       year === new Date().getFullYear();
        
        const dateStr = cellDate.toISOString().split('T')[0];
        const dayAppointments = appointments.filter(apt => {
            // Handle both date string formats
            const aptDate = apt.appointment_date.split('T')[0].split(' ')[0];
            return aptDate === dateStr;
        });
        
        const classes = ['calendar-day'];
        if (!isCurrentMonth) classes.push('other-month');
        if (isToday) classes.push('today');
        
        html += `<div class="${classes.join(' ')}" onclick="showDayDetail('${dateStr}', ${dayNumber}, '${monthNames[cellDate.getMonth()]}')">`;
        html += `<div class="day-number">${dayNumber}</div>`;
        
        // Show up to 3 appointments as colored boxes
        const maxShow = 3;
        dayAppointments.slice(0, maxShow).forEach(apt => {
            const statusClass = apt.status || 'scheduled';
            html += `<div class="appointment-box ${statusClass}" onclick="event.stopPropagation();">`;
            html += `${apt.patient?.name || 'Unknown'}`;
            html += `</div>`;
        });
        
        if (dayAppointments.length > maxShow) {
            html += `<div class="more-count">+${dayAppointments.length - maxShow} More</div>`;
        }
        
        html += `</div>`;
    }
    
    // Get the grid container and remove only calendar-day elements (keep headers)
    const grid = document.getElementById('calendarGrid');
    const existingDays = grid.querySelectorAll('.calendar-day, .more-count');
    existingDays.forEach(day => day.remove());
    
    // Add new calendar days as direct children after headers
    grid.insertAdjacentHTML('beforeend', html);
}

function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    
    // Fetch appointments for new month
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1;
    
    fetch(`{{ route('reception.appointments.calendar') }}?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            appointments = data.appointments;
            renderCalendar();
        })
        .catch(error => {
            console.error('Error fetching appointments:', error);
            renderCalendar();
        });
}

function showDayDetail(dateStr, dayNum, monthName) {
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    const modalDate = document.getElementById('modalDate');
    
    modalDate.textContent = `${monthName} ${dayNum}, ${currentDate.getFullYear()}`;
    modal.classList.add('show');
    
    console.log('Looking for appointments on:', dateStr);
    console.log('All appointments:', appointments);
    
    const dayAppointments = appointments.filter(apt => {
        console.log('Comparing:', apt.appointment_date, 'with', dateStr);
        // Compare just the date part, ignoring time
        const aptDate = apt.appointment_date.split('T')[0].split(' ')[0];
        return aptDate === dateStr;
    });
    
    console.log('Found appointments:', dayAppointments);
    
    if (dayAppointments.length === 0) {
        modalBody.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No appointments for this day</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    dayAppointments.sort((a, b) => a.appointment_time.localeCompare(b.appointment_time));
    
    dayAppointments.forEach(apt => {
        const statusClass = apt.status || 'scheduled';
        const statusText = apt.status ? apt.status.charAt(0).toUpperCase() + apt.status.slice(1) : 'Scheduled';
        
        html += `
            <div class="appointment-detail">
                <div class="appointment-detail-header">
                    <div class="patient-info">
                        <h6><i class="fas fa-user"></i> ${apt.patient?.name || 'Unknown Patient'}</h6>
                        <div class="time-info">
                            <i class="fas fa-clock"></i> ${apt.appointment_time}
                            ${apt.end_time ? ' - ' + apt.end_time : ''}
                        </div>
                    </div>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
                <div class="appointment-detail-body">
                    <div class="detail-row">
                        <i class="fas fa-stethoscope"></i>
                        <span>ID: <strong>#${apt.id}</strong></span>
                    </div>
                    ${apt.patient?.phone ? `
                    <div class="detail-row">
                        <i class="fas fa-phone"></i>
                        <span>${apt.patient.phone}</span>
                    </div>
                    ` : ''}
                    ${apt.patient?.email ? `
                    <div class="detail-row">
                        <i class="fas fa-envelope"></i>
                        <span>${apt.patient.email}</span>
                    </div>
                    ` : ''}
                    ${apt.reason ? `
                    <div class="detail-row">
                        <i class="fas fa-notes-medical"></i>
                        <span><strong>Reason:</strong> ${apt.reason}</span>
                    </div>
                    ` : ''}
                </div>
                <div class="appointment-actions">
                    <button class="action-btn reschedule" onclick="rescheduleAppointment(${apt.id})">
                        <i class="fas fa-calendar-alt"></i> Reschedule
                    </button>
                    <button class="action-btn cancel" onclick="cancelAppointment(${apt.id})">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    });
    
    modalBody.innerHTML = html;
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('show');
}

function updateStatus(appointmentId, status) {
    console.log('updateStatus called:', appointmentId, status);
    
    const url = `/reception/appointments/${appointmentId}/status`;
    console.log('Sending status update to:', url);
    
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        console.log('Status update response:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Status update data:', data);
        if (data.success) {
            showCustomAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showCustomAlert('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCustomAlert('An error occurred', 'error');
    });
}

function rescheduleAppointment(appointmentId) {
    console.log('Reschedule clicked for appointment:', appointmentId);
    
    // Store appointment ID for later use
    window.currentRescheduleId = appointmentId;
    
    // Open custom reschedule modal
    document.getElementById('rescheduleModal').classList.add('show');
    
    // Set default date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('rescheduleDate').value = tomorrow.toISOString().split('T')[0];
    
    // Set default time to 09:00
    document.getElementById('rescheduleTime').value = '09:00';
    
    // Focus on date input
    setTimeout(() => {
        document.getElementById('rescheduleDate').focus();
    }, 100);
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.remove('show');
    window.currentRescheduleId = null;
}

function submitReschedule() {
    const appointmentId = window.currentRescheduleId;
    const newDate = document.getElementById('rescheduleDate').value;
    const newTime = document.getElementById('rescheduleTime').value;
    
    console.log('Submitting reschedule:', appointmentId, newDate, newTime);
    
    // Validate inputs
    if (!newDate || !newTime) {
        showCustomAlert('Please select both date and time', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rescheduling...';
    submitBtn.disabled = true;
    
    const url = `/reception/appointments/${appointmentId}/reschedule`;
    console.log('Sending request to:', url);
    
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            appointment_date: newDate,
            appointment_time: newTime
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            closeRescheduleModal();
            showCustomAlert(data.message || 'Appointment rescheduled successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showCustomAlert(data.message || 'Failed to reschedule appointment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        showCustomAlert('An error occurred while rescheduling: ' + error.message, 'error');
    });
}

function showCustomAlert(message, type = 'info') {
    // Create custom alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert';
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function cancelAppointment(appointmentId) {
    console.log('Cancel clicked for appointment:', appointmentId);
    
    // Create custom confirmation modal
    showCustomConfirm(
        'Cancel Appointment',
        'Are you sure you want to cancel this appointment? This action cannot be undone.',
        () => {
            console.log('Proceeding with cancellation');
            updateStatus(appointmentId, 'cancelled');
        }
    );
}

function showCustomConfirm(title, message, onConfirm) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
    `;
    
    // Create modal
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        animation: scaleIn 0.2s ease;
    `;
    
    modal.innerHTML = `
        <div style="margin-bottom: 1rem;">
            <h4 style="margin: 0 0 0.75rem; color: #1f2937; font-size: 1.25rem; font-weight: 700;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> ${title}
            </h4>
            <p style="margin: 0; color: #6b7280; font-size: 0.95rem; line-height: 1.5;">${message}</p>
        </div>
        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <button id="confirmNo" style="padding: 0.75rem 1.5rem; border: 2px solid #e5e7eb; background: white; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-times"></i> No, Keep It
            </button>
            <button id="confirmYes" style="padding: 0.75rem 1.5rem; border: none; background: #ef4444; color: white; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-check"></i> Yes, Cancel It
            </button>
        </div>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    `;
    document.head.appendChild(style);
    
    // Handle buttons
    document.getElementById('confirmYes').onclick = () => {
        overlay.remove();
        style.remove();
        onConfirm();
    };
    
    document.getElementById('confirmNo').onclick = () => {
        overlay.remove();
        style.remove();
    };
    
    // Close on overlay click
    overlay.onclick = (e) => {
        if (e.target === overlay) {
            overlay.remove();
            style.remove();
        }
    };
}

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});

// ==================== SHARE FUNCTIONALITY ====================

function shareCalendar() {
    const modal = document.getElementById('shareModal');
    const linkInput = document.getElementById('calendarLink');
    
    // Generate calendar link
    const currentUrl = window.location.origin + window.location.pathname;
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1;
    const calendarUrl = `${currentUrl}?year=${year}&month=${month}`;
    
    linkInput.value = calendarUrl;
    modal.classList.add('show');
}

function closeShareModal() {
    document.getElementById('shareModal').classList.remove('show');
}

function copyCalendarLink() {
    const linkInput = document.getElementById('calendarLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile
    
    try {
        document.execCommand('copy');
        showCustomAlert('Link copied to clipboard!', 'success');
    } catch (err) {
        // Fallback for modern browsers
        navigator.clipboard.writeText(linkInput.value).then(() => {
            showCustomAlert('Link copied to clipboard!', 'success');
        }).catch(() => {
            showCustomAlert('Failed to copy link', 'error');
        });
    }
}

function shareViaEmail() {
    const year = currentDate.getFullYear();
    const month = currentDate.toLocaleString('default', { month: 'long' });
    const calendarUrl = document.getElementById('calendarLink').value;
    
    const subject = encodeURIComponent(`ClinicFlow Calendar - ${month} ${year}`);
    const body = encodeURIComponent(`Hi,\n\nHere's the appointment calendar for ${month} ${year}:\n\n${calendarUrl}\n\nBest regards,\nClinicFlow Team`);
    
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

function shareViaWhatsApp() {
    const year = currentDate.getFullYear();
    const month = currentDate.toLocaleString('default', { month: 'long' });
    const calendarUrl = document.getElementById('calendarLink').value;
    
    const message = encodeURIComponent(`*ClinicFlow Calendar - ${month} ${year}*\n\nView appointments: ${calendarUrl}`);
    
    window.open(`https://api.whatsapp.com/send?text=${message}`, '_blank');
}

function downloadCalendarPDF() {
    showCustomAlert('PDF download feature coming soon!', 'info');
    // Future: Implement PDF generation using jsPDF or server-side PDF generation
    // const year = currentDate.getFullYear();
    // const month = currentDate.getMonth() + 1;
    // window.location.href = `/reception/appointments/calendar/pdf?year=${year}&month=${month}`;
}

function printCalendar() {
    // Create a print-friendly version
    const printWindow = window.open('', '_blank');
    const year = currentDate.getFullYear();
    const month = currentDate.toLocaleString('default', { month: 'long' });
    
    // Get calendar HTML
    const calendarGrid = document.getElementById('calendarGrid').cloneNode(true);
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>ClinicFlow Calendar - ${month} ${year}</title>
            <style>
                @media print {
                    body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0; border: 1px solid #000; }
                    .day-header { background: #000; color: #fff; padding: 10px; text-align: center; font-weight: bold; border-right: 1px solid #000; }
                    .calendar-day { min-height: 100px; padding: 8px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; }
                    .day-number { font-weight: bold; margin-bottom: 5px; }
                    .appointment-box { background: #e0e0e0; padding: 4px; margin-bottom: 4px; font-size: 10px; border-left: 3px solid #000; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>ClinicFlow Calendar</h1>
                <h2>${month} ${year}</h2>
            </div>
            ${calendarGrid.outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

// Close share modal when clicking outside
document.getElementById('shareModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeShareModal();
    }
});

// Initial render
renderCalendar();
</script>
@endsection
