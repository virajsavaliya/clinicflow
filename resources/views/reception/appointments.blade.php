@extends('layouts.app')

@section('title', 'Appointments - ClinicFlow')

@section('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
    .view-tabs { display: flex; gap: 0.5rem; background: var(--color-gray-100); padding: 0.25rem; border-radius: 12px; border: 2px solid #e5e7eb; }
    .view-tab { padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.8rem; font-weight: 700; border: 2px solid transparent; background: transparent; cursor: pointer; color: var(--color-gray-600); transition: all 0.2s ease; }
    .view-tab.active { background: var(--color-white); color: var(--color-black); box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-color: #d1d5db; }
    .view-tab:hover:not(.active) { background: rgba(255,255,255,0.6); }
    .main-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; }
    .calendar-section { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .calendar-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--color-gray-200); background: var(--color-gray-50); }
    .calendar-nav { display: flex; align-items: center; gap: 0.5rem; }
    .calendar-nav button { background: var(--color-white); border: 2px solid #d1d5db; padding: 0.5rem 0.7rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s ease; }
    .calendar-nav button:hover { border-color: #1a1a1a; background: #f9fafb; transform: translateY(-1px); }
    .calendar-nav .current-date { font-weight: 600; min-width: 150px; text-align: center; }
    .appointments-list { max-height: 600px; overflow-y: auto; }
    .time-slot { display: flex; border-bottom: 1px solid var(--color-gray-100); min-height: 70px; transition: all 0.2s ease; }
    .time-slot:hover { background: #fafbfc; }
    .time-label { width: 90px; padding: 1rem 0.75rem; font-size: 0.75rem; color: var(--color-gray-500); border-right: 2px solid var(--color-gray-100); background: var(--color-gray-50); font-weight: 600; text-align: center; display: flex; align-items: flex-start; justify-content: center; }
    .slot-content { flex: 1; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .appointment-card { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 2px solid #93c5fd; border-left: 4px solid #3b82f6; padding: 0.75rem 1rem; border-radius: 10px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(59,130,246,0.15); position: relative; }
    .appointment-card:hover { transform: translateX(4px); box-shadow: 0 4px 12px rgba(59,130,246,0.3); border-color: #60a5fa; }
    .appointment-card.confirmed { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 2px solid #86efac; border-left: 4px solid #22c55e; }
    .appointment-card.confirmed:hover { box-shadow: 0 4px 12px rgba(34,197,94,0.3); border-color: #4ade80; }
    .appointment-card.scheduled { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 2px solid #93c5fd; border-left: 4px solid #3b82f6; }
    .appointment-card.completed { background: #f3f4f6; border: 2px solid #d1d5db; border-left: 4px solid #6b7280; opacity: 0.7; }
    .appointment-card.completed:hover { box-shadow: 0 4px 12px rgba(107,114,128,0.2); }
    .appointment-card .patient-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 0.25rem; color: #1a1a1a; display: flex; align-items: center; justify-content: space-between; }
    .appointment-card .apt-meta { font-size: 0.75rem; color: #4b5563; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
    .appointment-card .status-badge { font-size: 0.65rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    .appointment-card.confirmed .status-badge { background: #22c55e; color: white; }
    .appointment-card.scheduled .status-badge { background: #3b82f6; color: white; }
    .appointment-card.completed .status-badge { background: #6b7280; color: white; }
    .appointment-card .contact-info { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(0,0,0,0.1); }
    .contact-btn { padding: 0.3rem 0.6rem; font-size: 0.7rem; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.3rem; }
    .contact-btn i { font-size: 0.8rem; }
    .contact-btn.email { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .contact-btn.email:hover { background: #fde68a; transform: scale(1.05); box-shadow: 0 2px 6px rgba(146,64,14,0.2); }
    .contact-btn.whatsapp { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .contact-btn.whatsapp:hover { background: #a7f3d0; transform: scale(1.05); box-shadow: 0 2px 6px rgba(6,95,70,0.2); }
    .contact-btn.sms { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .contact-btn.sms:hover { background: #c7d2fe; transform: scale(1.05); box-shadow: 0 2px 6px rgba(55,48,163,0.2); }
    .appointment-card .patient-name { font-weight: 600; font-size: 0.8rem; margin-bottom: 0.125rem; }
    .appointment-card .apt-meta { font-size: 0.7rem; color: var(--color-gray-600); }
    
    /* Redesigned Stats Row */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
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
    
    .side-panel { display: flex; flex-direction: column; gap: 1rem; }
    .upcoming-card { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .upcoming-card .header { padding: 0.875rem 1rem; border-bottom: 1px solid var(--color-gray-200); font-weight: 600; font-size: 0.875rem; background: var(--color-gray-50); }
    .upcoming-list { max-height: 300px; overflow-y: auto; }
    .upcoming-item { display: flex; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-gray-100); cursor: pointer; transition: background var(--transition-fast); }
    .upcoming-item:hover { background: var(--color-gray-50); }
    .upcoming-item .date-box { background: var(--color-black); color: var(--color-white); padding: 0.5rem; border-radius: var(--radius-md); text-align: center; min-width: 48px; }
    .upcoming-item .date-box .day { font-size: 1rem; font-weight: 700; line-height: 1; }
    .upcoming-item .date-box .month { font-size: 0.6rem; text-transform: uppercase; }
    .upcoming-item .info h6 { font-size: 0.8rem; font-weight: 600; margin: 0 0 0.25rem; }
    .upcoming-item .info small { font-size: 0.7rem; color: var(--color-gray-500); }
    .quick-book { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .quick-book h5 { font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    
    /* Improved Empty State */
    .empty-state { padding: 4rem 2rem; text-align: center; }
    .empty-state-icon { width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .empty-state-icon i { font-size: 2rem; color: #9ca3af; }
    .empty-state h5 { font-size: 1.125rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
    .empty-state p { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
    .btn-schedule { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; font-size: 0.9375rem; font-weight: 700; color: #fff; background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); border: 2px solid #1a1a1a; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .btn-schedule:hover { background: linear-gradient(135deg, #333 0%, #444 100%); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.25); border-color: #000; }
    .btn-schedule i { font-size: 1.125rem; }
    
    /* Custom Modal - Professional Design */
    .custom-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 4rem 1rem; }
    .custom-modal.show { display: flex; }
    .custom-modal-dialog { background: #ffffff; border-radius: 16px; width: 100%; max-width: 600px; max-height: 100%; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 3px solid #e5e7eb; margin: auto; }
    .custom-modal-header { flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: #ffffff; }
    .custom-modal-header h5 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .custom-modal-header h5 i { font-size: 1.25rem; }
    .custom-modal-header .btn-close { background: rgba(255,255,255,0.1) !important; border: none !important; cursor: pointer; color: white !important; width: 32px; height: 32px; display: flex !important; align-items: center; justify-content: center; border-radius: 8px; opacity: 1 !important; transition: all 0.2s ease; line-height: 1; }
    .custom-modal-header .btn-close:hover { background: rgba(255,255,255,0.25) !important; transform: rotate(90deg); }
    .custom-modal-header .btn-close::before { content: "×"; font-size: 1.75rem; line-height: 1; display: block; }
    .custom-modal-body { flex: 1; overflow-y: auto; padding: 1.5rem; background: #ffffff; }
    .custom-modal-body .form-label { display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #1f2937; }
    .custom-modal-body .form-control, .custom-modal-body .form-select { width: 100%; padding: 0.75rem 0.9rem; border: 2px solid #d1d5db; background: #ffffff; color: #000000; font-size: 0.9rem; border-radius: 10px; transition: all 0.2s ease; }
    .custom-modal-body .form-control:focus, .custom-modal-body .form-select:focus { outline: none; border-color: #1a1a1a; box-shadow: 0 0 0 4px rgba(26,26,26,0.1); }
    .custom-modal-body .form-control::placeholder { color: #9ca3af; }
    .custom-modal-body textarea.form-control { resize: vertical; min-height: 80px; }
    .custom-modal-footer { flex-shrink: 0; padding: 1rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; flex-direction: column; gap: 0.75rem; }
    .patient-search-results { position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 2px solid #d1d5db; border-radius: 10px; max-height: 200px; overflow-y: auto; z-index: 1100; box-shadow: 0 8px 24px rgba(0,0,0,0.15); margin-top: 4px; }
    .patient-search-results a { display: block; padding: 0.75rem 1rem; text-decoration: none; color: #1f2937; font-weight: 500; transition: all 0.2s ease; border-bottom: 1px solid #f3f4f6; }
    .patient-search-results a:last-child { border-bottom: none; }
    .patient-search-results a:hover { background: #f3f4f6; color: #1a1a1a; padding-left: 1.25rem; }
    
    /* Professional Button Styles */
    .btn { padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; border-radius: 10px; border: 2px solid transparent; transition: all 0.2s ease; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .btn-dark { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #ffffff; border-color: #1a1a1a; }
    .btn-dark:hover { background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%); border-color: #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
    .btn-secondary { background: #f3f4f6; color: #1f2937; border-color: #d1d5db; }
    .btn-secondary:hover { background: #e5e7eb; border-color: #9ca3af; }
    .btn-outline-secondary { background: transparent; color: #6b7280; border-color: #d1d5db; }
    .btn-outline-secondary:hover { background: #f9fafb; border-color: #9ca3af; color: #374151; }
    .btn-outline-dark { background: transparent; color: #1a1a1a; border-color: #1a1a1a; }
    .btn-outline-dark:hover { background: #1a1a1a; color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.8125rem; }
    
    /* Animations */
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    @media (max-width: 1024px) { .main-grid { grid-template-columns: 1fr; } .stats-row { grid-template-columns: repeat(2, 1fr); } }
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
            <button class="view-tab {{ $view === 'day' ? 'active' : '' }}" onclick="changeView('day')">Day</button>
            <button class="view-tab {{ $view === 'week' ? 'active' : '' }}" onclick="changeView('week')">Week</button>
            <button class="view-tab {{ $view === 'month' ? 'active' : '' }}" onclick="changeView('month')">Month</button>
            <button class="view-tab" onclick="window.location.href='{{ route('reception.appointments.calendar') }}'">
                <i class="bi bi-grid-3x3-gap"></i> Calendar
            </button>
        </div>
        <button class="btn btn-dark" onclick="openNewAppointmentModal()">
            <i class="bi bi-plus-lg"></i> New Appointment
        </button>
    </div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon today"><i class="bi bi-calendar-day"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['total'] }}</div>
            <div class="label">Today's Appointments</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon confirmed"><i class="bi bi-check-circle"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['confirmed'] }}</div>
            <div class="label">Confirmed</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon pending"><i class="bi bi-clock-history"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon completed"><i class="bi bi-check2-all"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayStats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
    </div>
</div>

<div class="main-grid">
    <div class="calendar-section">
        <div class="calendar-header">
            <div class="calendar-nav">
                <button onclick="navigateDate(-1)"><i class="bi bi-chevron-left"></i></button>
                <span class="current-date">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
                <button onclick="navigateDate(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-secondary" onclick="goToToday()">Today</button>
            </div>
        </div>
        <div class="appointments-list">
            @php
                $timeSlots = [];
                for ($h = 8; $h <= 20; $h++) {
                    $timeSlots[] = sprintf('%02d:00', $h);
                    $timeSlots[] = sprintf('%02d:30', $h);
                }
            @endphp
            
            @if($appointments->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h5>No appointments</h5>
                    <p>No appointments scheduled for this day</p>
                    <button class="btn-schedule" onclick="openNewAppointmentModal()">
                        <i class="bi bi-plus-lg"></i>
                        Schedule Appointment
                    </button>
                </div>
            @else
                @foreach($timeSlots as $slot)
                    @php
                        // Match appointments to the nearest half-hour slot
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
                    <div class="time-slot">
                        <div class="time-label">{{ \Carbon\Carbon::parse($slot)->format('h:i A') }}</div>
                        <div class="slot-content">
                            @foreach($slotAppointments as $apt)
                                <div class="appointment-card {{ $apt->status }}" onclick="viewAppointment({{ $apt->id }})">
                                    <div class="patient-name">
                                        <span><i class="bi bi-person-fill"></i> {{ $apt->patient->name }}</span>
                                        <span class="status-badge">{{ ucfirst($apt->status) }}</span>
                                    </div>
                                    <div class="apt-meta">
                                        <span><i class="bi bi-clock-fill"></i> {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</span>
                                        @if($apt->reason) 
                                            <span><i class="bi bi-chat-text-fill"></i> {{ Str::limit($apt->reason, 30) }}</span>
                                        @endif
                                        @if($apt->patient->phone)
                                            <span><i class="bi bi-telephone-fill"></i> {{ $apt->patient->phone }}</span>
                                        @endif
                                    </div>
                                    @if($apt->patient->phone || $apt->patient->email)
                                        <div class="contact-info" onclick="event.stopPropagation()">
                                            <small style="color: #6b7280; font-weight: 600; margin-right: 0.25rem;">Quick Contact:</small>
                                            @if($apt->patient->email)
                                                <button class="contact-btn email" onclick="sendEmailNotification({{ $apt->id }}, '{{ $apt->patient->email }}')">
                                                    <i class="bi bi-envelope-fill"></i> Email
                                                </button>
                                            @endif
                                            @if($apt->patient->phone)
                                                <button class="contact-btn whatsapp" onclick="sendWhatsAppMessage('{{ $apt->patient->phone }}', '{{ $apt->patient->name }}', '{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}', '{{ $apt->appointment_date->format('Y-m-d') }}')">
                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                </button>
                                                <button class="contact-btn sms" onclick="sendSMS('{{ $apt->patient->phone }}', '{{ $apt->patient->name }}', '{{ $apt->appointment_date->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}')">
                                                    <i class="bi bi-chat-dots-fill"></i> SMS
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="side-panel">
        <div class="upcoming-card">
            <div class="header"><i class="bi bi-calendar-week"></i> Upcoming Appointments</div>
            <div class="upcoming-list">
                @forelse($upcomingAppointments as $apt)
                    <div class="upcoming-item" onclick="viewAppointment({{ $apt->id }})">
                        <div class="date-box">
                            <div class="day">{{ $apt->appointment_date->format('d') }}</div>
                            <div class="month">{{ $apt->appointment_date->format('M') }}</div>
                        </div>
                        <div class="info">
                            <h6>{{ $apt->patient->name }}</h6>
                            <small>
                                <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}
                                <span class="badge bg-{{ $apt->status === 'confirmed' ? 'success' : ($apt->status === 'scheduled' ? 'info' : 'secondary') }} ms-1">{{ ucfirst($apt->status) }}</span>
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted">
                        <small>No upcoming appointments</small>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="quick-book">
            <h5><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
            <div class="d-grid gap-2">
                <button class="btn btn-dark btn-sm" onclick="openNewAppointmentModal()">
                    <i class="bi bi-plus"></i> Book Appointment
                </button>
                <a href="{{ route('reception.patients') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person-plus"></i> Add New Patient
                </a>
                <a href="{{ route('reception.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-speedometer2"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- New Appointment Modal (custom implementation to fix auto-close) -->
<div class="custom-modal" id="newAppointmentModal">
    <div class="custom-modal-dialog" onclick="event.stopPropagation()">
        <div class="custom-modal-header">
            <h5><i class="bi bi-calendar-plus"></i> New Appointment</h5>
            <button type="button" class="btn-close" onclick="closeModal('newAppointmentModal')"></button>
        </div>
        <div class="custom-modal-body">
            <form id="newAppointmentForm" onsubmit="return false;">
                <div class="mb-3" style="position: relative;">
                    <label class="form-label"><i class="bi bi-person-circle"></i> Patient *</label>
                    <input type="text" class="form-control" id="patientSearch" placeholder="Search patient by name or phone..." autocomplete="off">
                    <input type="hidden" id="patientId" name="patient_id">
                    <div id="patientSearchResults" class="patient-search-results" style="display:none;"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-calendar-event"></i> Date *</label>
                        <input type="date" class="form-control" id="appointmentDate" name="appointment_date" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-clock"></i> Time *</label>
                        <select class="form-select" id="appointmentTime" name="appointment_time" required>
                            <option value="">Select date first</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-chat-text"></i> Reason for Visit</label>
                    <input type="text" class="form-control" id="appointmentReason" name="reason" placeholder="e.g., Follow-up, Consultation">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-sticky"></i> Notes</label>
                    <textarea class="form-control" id="appointmentNotes" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                </div>
                <div class="form-check mb-3" style="padding-left: 0;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600; color: #374151;">
                        <input type="checkbox" class="form-check-input" id="isRecurring" name="is_recurring" style="width: 18px; height: 18px; margin: 0; cursor: pointer; border: 2px solid #d1d5db; border-radius: 4px;">
                        <i class="bi bi-arrow-repeat"></i> Recurring Appointment
                    </label>
                </div>
                <div id="recurringOptions" style="display:none; background: #f9fafb; padding: 1rem; border-radius: 10px; border: 2px solid #e5e7eb; margin-top: 0.5rem;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-calendar-range"></i> Frequency</label>
                            <select class="form-select" id="recurringFrequency" name="recurring_frequency">
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Every 2 Weeks</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-calendar-x"></i> Until</label>
                            <input type="date" class="form-control" id="recurringEndDate" name="recurring_end_date">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-outline-secondary" onclick="closeModal('newAppointmentModal')" style="font-weight: 600;">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button type="button" class="btn btn-dark" onclick="saveAppointment()" style="font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                <i class="bi bi-check-lg"></i> Book Appointment
            </button>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="custom-modal" id="viewAppointmentModal">
    <div class="custom-modal-dialog" onclick="event.stopPropagation()">
        <div class="custom-modal-header">
            <h5><i class="bi bi-calendar-event"></i> Appointment Details</h5>
            <button type="button" class="btn-close" onclick="closeModal('viewAppointmentModal')"></button>
        </div>
        <div class="custom-modal-body" id="appointmentDetails">
            <!-- Filled by JS -->
        </div>
        <div class="custom-modal-footer" id="appointmentActions">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="custom-modal" id="rescheduleModal">
    <div class="custom-modal-dialog" onclick="event.stopPropagation()" style="max-width: 500px;">
        <div class="custom-modal-header">
            <h5><i class="fas fa-calendar-alt"></i> Reschedule Appointment</h5>
            <button type="button" class="btn-close" onclick="closeRescheduleModal()"></button>
        </div>
        <div class="custom-modal-body" style="padding: 1.5rem;">
            <div class="reschedule-form">
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                        <i class="fas fa-calendar"></i> New Date
                    </label>
                    <input type="date" id="rescheduleDate" class="form-control" 
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem;"
                           min="{{ date('Y-m-d') }}" onchange="fetchRescheduleSlots()">
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                        <i class="fas fa-clock"></i> New Time
                    </label>
                    <select id="rescheduleTime" class="form-control" disabled
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem;">
                        <option value="">Select Date First</option>
                    </select>
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
@endsection

@section('scripts')
<script>
let currentDate = '{{ $date }}';
let currentView = '{{ $view }}';
let selectedAppointment = null;

function changeView(view) {
    window.location.href = `{{ route('reception.appointments') }}?date=${currentDate}&view=${view}`;
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
    window.location.href = `{{ route('reception.appointments') }}?date=${newDate}&view=${currentView}`;
}

function goToToday() {
    window.location.href = `{{ route('reception.appointments') }}?view=${currentView}`;
}

function openNewAppointmentModal() {
    document.getElementById('newAppointmentForm').reset();
    document.getElementById('patientId').value = '';
    document.getElementById('appointmentTime').innerHTML = '<option value="">Select date first</option>';
    document.getElementById('recurringOptions').style.display = 'none';
    document.getElementById('patientSearchResults').style.display = 'none';
    document.getElementById('newAppointmentModal').classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

// Patient search with debounce
let searchTimeout;
document.getElementById('patientSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    if (query.length < 2) {
        document.getElementById('patientSearchResults').style.display = 'none';
        return;
    }
    searchTimeout = setTimeout(async () => {
        try {
            const res = await axios.get('/reception/search-patients?q=' + encodeURIComponent(query));
            const results = document.getElementById('patientSearchResults');
            if (res.data.length > 0) {
                results.innerHTML = res.data.map(p => `
                    <a href="#" onclick="selectPatient(${p.id}, '${p.name.replace(/'/g, "\\'")}'); return false;">
                        <strong>${p.name}</strong> ${p.phone ? '<span class="text-muted">(' + p.phone + ')</span>' : ''}
                    </a>
                `).join('');
                results.style.display = 'block';
            } else {
                results.innerHTML = '<div class="p-2 text-muted">No patients found</div>';
                results.style.display = 'block';
            }
        } catch (e) {
            console.error(e);
        }
    }, 300);
});

// Close patient search results when clicking outside
document.addEventListener('click', function(e) {
    const searchContainer = document.getElementById('patientSearch').parentElement;
    if (!searchContainer.contains(e.target)) {
        document.getElementById('patientSearchResults').style.display = 'none';
    }
});

function selectPatient(id, name) {
    document.getElementById('patientId').value = id;
    document.getElementById('patientSearch').value = name;
    document.getElementById('patientSearchResults').style.display = 'none';
}

// Load available slots when date changes
document.getElementById('appointmentDate').addEventListener('change', async function(e) {
    const date = e.target.value;
    if (!date) return;
    
    try {
        const res = await axios.get(`{{ route('reception.appointments.slots') }}?date=${date}`);
        const select = document.getElementById('appointmentTime');
        
        if (res.data.available && res.data.slots.length > 0) {
            select.innerHTML = '<option value="">Select time</option>' + 
                res.data.slots.map(slot => {
                    const time = new Date('2000-01-01 ' + slot);
                    return `<option value="${slot}">${time.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</option>`;
                }).join('');
        } else {
            select.innerHTML = `<option value="">${res.data.message || 'No slots available'}</option>`;
        }
    } catch (e) {
        console.error(e);
        // Fallback: show all time slots
        const select = document.getElementById('appointmentTime');
        let options = '<option value="">Select time</option>';
        for (let h = 8; h <= 20; h++) {
            for (let m of ['00', '30']) {
                const time24 = `${String(h).padStart(2, '0')}:${m}`;
                const hour12 = h > 12 ? h - 12 : (h === 0 ? 12 : h);
                const ampm = h >= 12 ? 'PM' : 'AM';
                options += `<option value="${time24}">${hour12}:${m} ${ampm}</option>`;
            }
        }
        select.innerHTML = options;
    }
});

// Recurring toggle
document.getElementById('isRecurring').addEventListener('change', function(e) {
    document.getElementById('recurringOptions').style.display = e.target.checked ? 'block' : 'none';
});

async function saveAppointment() {
    const patientId = document.getElementById('patientId').value;
    
    if (!patientId) {
        window.showGlobalNotification ? window.showGlobalNotification('Please select a patient', 'error') : alert('Please select a patient');
        return;
    }
    
    const appointmentDate = document.getElementById('appointmentDate').value;
    const appointmentTime = document.getElementById('appointmentTime').value;
    
    if (!appointmentDate || !appointmentTime) {
        window.showGlobalNotification ? window.showGlobalNotification('Please select date and time', 'error') : alert('Please select date and time');
        return;
    }
    
    const data = {
        patient_id: patientId,
        appointment_date: appointmentDate,
        appointment_time: appointmentTime,
        reason: document.getElementById('appointmentReason').value,
        notes: document.getElementById('appointmentNotes').value,
        is_recurring: document.getElementById('isRecurring').checked,
        recurring_frequency: document.getElementById('recurringFrequency').value,
        recurring_end_date: document.getElementById('recurringEndDate').value,
    };
    
    try {
        const res = await axios.post('{{ route('reception.appointments.store') }}', data);
        if (res.data.success) {
            window.showGlobalNotification ? window.showGlobalNotification(res.data.message, 'success') : alert(res.data.message);
            closeModal('newAppointmentModal');
            
            // Ask if user wants to send notification
            const appointment = res.data.appointment;
            if (appointment && appointment.patient && appointment.patient.phone) {
                showNotificationPrompt(appointment, 'created');
            } else {
                setTimeout(() => location.reload(), 1000);
            }
        }
    } catch (e) {
        const msg = e.response?.data?.message || 'Error booking appointment';
        window.showGlobalNotification ? window.showGlobalNotification(msg, 'error') : alert(msg);
    }
}

async function viewAppointment(id) {
    try {
        const modal = document.getElementById('viewAppointmentModal');
        const detailsDiv = document.getElementById('appointmentDetails');
        const actionsDiv = document.getElementById('appointmentActions');
        
        detailsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        actionsDiv.innerHTML = '';
        modal.classList.add('show');
        
        const response = await axios.get(`/api/appointments/${id}`);
        const apt = response.data;
        
        // Display appointment details with professional form layout
        detailsDiv.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Patient Header Section -->
                <div style="text-align: center; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                            ${apt.patient.name.charAt(0).toUpperCase()}
                        </div>
                        <div style="text-align: left;">
                            <div style="font-weight: 700; font-size: 1rem; color: #1f2937;">${apt.patient.name}</div>
                            ${apt.patient.phone ? `<div style="font-size: 0.875rem; color: #6b7280;"><i class="bi bi-telephone"></i> ${apt.patient.phone}</div>` : ''}
                        </div>
                    </div>
                    <span class="status-badge" style="display: inline-block; background: ${apt.status === 'confirmed' ? '#dcfce7' : apt.status === 'scheduled' ? '#dbeafe' : apt.status === 'cancelled' ? '#fee2e2' : '#f3f4f6'}; color: ${apt.status === 'confirmed' ? '#166534' : apt.status === 'scheduled' ? '#1e40af' : apt.status === 'cancelled' ? '#991b1b' : '#374151'}; padding: 0.4rem 0.875rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">${apt.status.toUpperCase()}</span>
                </div>

                <!-- Date & Time Section (2-Column Grid) -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div style="padding: 1rem; background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%); border: 1px solid #dbeafe; border-radius: 10px; text-align: center;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                        <div style="font-weight: 700; font-size: 1.1rem; color: #1f2937;">
                            <i class="bi bi-calendar3" style="margin-right: 0.35rem; color: #3b82f6;"></i>
                            ${new Date(apt.appointment_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}
                        </div>
                    </div>
                    <div style="padding: 1rem; background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%); border: 1px solid #fcd34d; border-radius: 10px; text-align: center;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Time</div>
                        <div style="font-weight: 700; font-size: 1.1rem; color: #1f2937;">
                            <i class="bi bi-clock" style="margin-right: 0.35rem; color: #f59e0b;"></i>
                            ${new Date('1970-01-01T' + apt.appointment_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                </div>

                <!-- Doctor Field -->
                ${apt.doctor ? `
                    <div style="padding: 1rem; background: linear-gradient(135deg, #ede9fe 0%, #ffffff 100%); border: 1px solid #ddd6fe; border-radius: 10px;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Doctor</div>
                        <div style="font-weight: 700; font-size: 1rem; color: #1f2937;">
                            <i class="bi bi-person-badge" style="margin-right: 0.35rem; color: #a78bfa;"></i> Dr. ${apt.doctor.name}
                        </div>
                    </div>
                ` : ''}

                <!-- Reason Field -->
                ${apt.reason ? `
                    <div style="padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Reason for Visit</div>
                        <div style="color: #374151; font-weight: 500; line-height: 1.5;">
                            <i class="bi bi-chat-dots" style="margin-right: 0.35rem; color: #6b7280;"></i> ${apt.reason}
                        </div>
                    </div>
                ` : ''}

                <!-- Notes Field -->
                ${apt.notes ? `
                    <div style="padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Additional Notes</div>
                        <div style="color: #374151; font-weight: 500; line-height: 1.6;">
                            <i class="bi bi-sticky" style="margin-right: 0.35rem; color: #6b7280;"></i> ${apt.notes}
                        </div>
                    </div>
                ` : ''}

                <!-- Email Field (if available) -->
                ${apt.patient.email ? `
                    <div style="padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;">
                        <div style="font-size: 0.7rem; color: #6b7280; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</div>
                        <div style="color: #374151; font-weight: 500;">
                            <i class="bi bi-envelope" style="margin-right: 0.35rem; color: #6b7280;"></i> ${apt.patient.email}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
        
        // Add communication buttons if patient has phone
        let communicationButtons = '';
        if (apt.patient.phone) {
            communicationButtons = `
                <div style="padding: 1.25rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 2px dashed #cbd5e1; border-radius: 10px;">
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="bi bi-send"></i> Send Notification
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <button onclick="sendWhatsAppMessage('${apt.patient.phone}', '${apt.patient.name}', '${new Date('1970-01-01T' + apt.appointment_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}', '${apt.appointment_date}')" 
                                style="padding: 0.85rem; background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.875rem; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2);">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </button>
                        <button onclick="sendSMS('${apt.patient.phone}', '${apt.patient.name}', '${apt.appointment_date}', '${new Date('1970-01-01T' + apt.appointment_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}')" 
                                style="padding: 0.85rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.875rem; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);">
                            <i class="bi bi-chat-dots-fill"></i> SMS
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Add action buttons (Reschedule & Cancel)
        if (apt.status !== 'cancelled' && apt.status !== 'completed') {
            actionsDiv.innerHTML = communicationButtons + `
                <div style="display: flex; gap: 0.75rem; margin-top: ${apt.patient.phone ? '1rem' : '0'}; padding-top: ${apt.patient.phone ? '1rem' : '0'}; border-top: ${apt.patient.phone ? '1px solid #e5e7eb' : 'none'};">
                    <button class="btn btn-dark" onclick="closeModal('viewAppointmentModal'); rescheduleFromView(${apt.id});" style="flex: 1; padding: 0.85rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(31, 41, 55, 0.15);">
                        <i class="bi bi-calendar-event"></i> Reschedule
                    </button>
                    <button class="btn" onclick="cancelFromView(${apt.id})" style="flex: 1; padding: 0.85rem; background: #fee2e2; color: #991b1b; border: 2px solid #fecaca; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1); cursor: pointer; border-radius: 6px; transition: all 0.2s ease;">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            `;
        } else {
            actionsDiv.innerHTML = communicationButtons + `
                <div style="display: flex; gap: 0.75rem; margin-top: ${apt.patient.phone ? '1rem' : '0'}; padding-top: ${apt.patient.phone ? '1rem' : '0'}; border-top: ${apt.patient.phone ? '1px solid #e5e7eb' : 'none'};">
                    <button class="btn btn-outline-secondary" onclick="closeModal('viewAppointmentModal')" style="flex: 1; padding: 0.85rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-x"></i> Close
                    </button>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error loading appointment:', error);
        document.getElementById('appointmentDetails').innerHTML = `
            <div class="text-center" style="color: #ef4444;">
                <i class="bi bi-exclamation-circle"></i> Error loading appointment details
            </div>
        `;
    }
}

// Close modal on background click only (not dialog click)
document.querySelectorAll('.custom-modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Prevent modal close when clicking inside form elements
document.querySelectorAll('.custom-modal-dialog').forEach(dialog => {
    dialog.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

// ==================== NOTIFICATION FUNCTIONS ====================

// Send Email Notification
async function sendEmailNotification(appointmentId, email) {
    if (!email) {
        window.showGlobalNotification ? window.showGlobalNotification('No email address available', 'error') : alert('No email address');
        return;
    }
    
    if (confirm(`Send appointment confirmation email to ${email}?`)) {
        try {
            // You can create a route for this or use a third-party service
            const response = await axios.post('/api/send-appointment-email', {
                appointment_id: appointmentId,
                email: email
            });
            
            window.showGlobalNotification ? 
                window.showGlobalNotification('Email sent successfully!', 'success') : 
                alert('Email sent successfully!');
        } catch (error) {
            console.error('Email error:', error);
            window.showGlobalNotification ? 
                window.showGlobalNotification('Email sending is not configured yet. Please set up email service.', 'info') : 
                alert('Email service not configured');
        }
    }
}

// Send WhatsApp Message
function sendWhatsAppMessage(phone, patientName, time, date) {
    if (!phone) {
        window.showGlobalNotification ? window.showGlobalNotification('No phone number available', 'error') : alert('No phone number');
        return;
    }
    
    // Format phone number - remove all non-numeric characters
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    
    // If phone number doesn't start with country code, add it
    // For Indian numbers (10 digits), add 91
    if (cleanPhone.length === 10) {
        cleanPhone = '91' + cleanPhone;
    }
    // If it starts with 0, remove it and add country code
    else if (cleanPhone.startsWith('0') && cleanPhone.length === 11) {
        cleanPhone = '91' + cleanPhone.substring(1);
    }
    // If already has 91 prefix but with extra digits, clean it
    else if (cleanPhone.startsWith('91') && cleanPhone.length > 12) {
        cleanPhone = cleanPhone.substring(0, 12);
    }
    
    // Calculate date text (today/tomorrow/specific date)
    const appointmentDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    let dateText;
    if (appointmentDate.toDateString() === today.toDateString()) {
        dateText = 'today';
    } else if (appointmentDate.toDateString() === tomorrow.toDateString()) {
        dateText = 'tomorrow';
    } else {
        const options = { weekday: 'long', month: 'long', day: 'numeric' };
        dateText = 'on ' + appointmentDate.toLocaleDateString('en-US', options);
    }
    
    // Create appointment message
    const message = `Hello ${patientName},

This is a reminder for your appointment at ${time} ${dateText}.

ClinicFlow - Your Healthcare Partner

For any queries, please contact us.`;
    
    // Use api.whatsapp.com (more reliable than wa.me)
    const whatsappUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
    
    // Open WhatsApp in new window
    window.open(whatsappUrl, '_blank');
}

// Send SMS
function sendSMS(phone, patientName, date, time) {
    if (!phone) {
        window.showGlobalNotification ? window.showGlobalNotification('No phone number available', 'error') : alert('No phone number');
        return;
    }
    
    // Calculate date text (today/tomorrow/specific date)
    const appointmentDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    let dateText;
    if (appointmentDate.toDateString() === today.toDateString()) {
        dateText = 'today';
    } else if (appointmentDate.toDateString() === tomorrow.toDateString()) {
        dateText = 'tomorrow';
    } else {
        const options = { month: 'short', day: 'numeric' };
        dateText = 'on ' + appointmentDate.toLocaleDateString('en-US', options);
    }
    
    const message = `Hello ${patientName}, reminder for your appointment ${dateText} at ${time}. - ClinicFlow`;
    const smsUrl = `sms:${phone}?body=${encodeURIComponent(message)}`;
    window.location.href = smsUrl;
}

// Show notification prompt after appointment actions
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

function rescheduleFromView(appointmentId) {
    console.log('Reschedule clicked for appointment:', appointmentId);
    
    // Store appointment ID for later use
    window.currentRescheduleId = appointmentId;
    
    // Open custom reschedule modal
    document.getElementById('rescheduleModal').classList.add('show');
    
    // Reset date and time fields
    document.getElementById('rescheduleDate').value = '';
    const rescheduleTimeEl = document.getElementById('rescheduleTime');
    rescheduleTimeEl.innerHTML = '<option value="">Select Date First</option>';
    rescheduleTimeEl.disabled = true;
    
    // Focus on date input
    setTimeout(() => {
        document.getElementById('rescheduleDate').focus();
    }, 100);
}

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

    const excludeId = window.currentRescheduleId ? `&exclude_id=${window.currentRescheduleId}` : '';
    axios.get(`{{ route('reception.appointments.slots') }}?date=${date}${excludeId}`)
        .then(res => {
            if (res.data.available && res.data.slots.length > 0) {
                timeSelect.innerHTML = '<option value="">Select time</option>' +
                    res.data.slots.map(slot => {
                        const time = new Date('2000-01-01 ' + slot);
                        return `<option value="${slot}">${time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</option>`;
                    }).join('');
                timeSelect.disabled = false;
            } else {
                timeSelect.innerHTML = `<option value="">${res.data.message || 'No slots available'}</option>`;
            }
        })
        .catch(() => {
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        });
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
    const submitBtn = document.querySelector('#rescheduleModal button[onclick="submitReschedule()"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rescheduling...';
        submitBtn.disabled = true;
    }
    
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
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        
        if (data.success) {
            closeRescheduleModal();
            showCustomAlert(data.message || 'Appointment rescheduled successfully!', 'success');
            
            // Ask if user wants to send notification
            const appointment = data.appointment;
            if (appointment && appointment.patient && appointment.patient.phone) {
                setTimeout(() => showNotificationPrompt(appointment, 'rescheduled'), 1500);
            } else {
                setTimeout(() => location.reload(), 1500);
            }
        } else {
            showCustomAlert(data.message || 'Failed to reschedule appointment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
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

function cancelFromView(appointmentId) {
    console.log('Cancel clicked for appointment:', appointmentId);
    
    // Create custom confirmation modal
    showCustomConfirm(
        'Cancel Appointment',
        'Are you sure you want to cancel this appointment? This action cannot be undone.',
        () => {
            console.log('Proceeding with cancellation');
            updateAppointmentStatus(appointmentId, 'cancelled');
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
    
    // Create dialog
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: scaleIn 0.2s ease;
    `;
    
    dialog.innerHTML = `
        <h5 style="margin: 0 0 1rem; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-exclamation-triangle" style="color: #f59e0b;"></i>
            ${title}
        </h5>
        <p style="margin: 0 0 1.5rem; color: #6b7280; line-height: 1.5;">${message}</p>
        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <button id="confirmCancelBtn" style="padding: 0.625rem 1.25rem; background: #f3f4f6; color: #1f2937; border: 2px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                No, Keep It
            </button>
            <button id="confirmOkBtn" style="padding: 0.625rem 1.25rem; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                Yes, Cancel It
            </button>
        </div>
    `;
    
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);
    
    // Handle buttons
    document.getElementById('confirmCancelBtn').onclick = () => {
        overlay.remove();
    };
    
    document.getElementById('confirmOkBtn').onclick = () => {
        overlay.remove();
        onConfirm();
    };
    
    // Close on overlay click
    overlay.onclick = (e) => {
        if (e.target === overlay) {
            overlay.remove();
        }
    };
}

function updateAppointmentStatus(appointmentId, status) {
    const url = `/reception/appointments/${appointmentId}/status`;
    
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCustomAlert(data.message || 'Appointment cancelled successfully!', 'success');
            closeModal('viewAppointmentModal');
            
            // Ask if user wants to send cancellation notification
            const appointment = data.appointment;
            if (appointment && appointment.patient && appointment.patient.phone) {
                setTimeout(() => showNotificationPrompt(appointment, 'cancelled'), 1500);
            } else {
                setTimeout(() => location.reload(), 1500);
            }
        } else {
            showCustomAlert(data.message || 'Failed to cancel appointment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCustomAlert('An error occurred: ' + error.message, 'error');
    });
}

// Auto-send notification when appointment is created
function sendAppointmentNotification(patientPhone, patientEmail, patientName, appointmentDate, appointmentTime) {
    if (patientPhone) {
        // Format phone number - remove all non-numeric characters
        let cleanPhone = patientPhone.replace(/[^0-9]/g, '');
        
        // If phone number doesn't start with country code, add it
        if (cleanPhone.length === 10) {
            cleanPhone = '91' + cleanPhone;
        } else if (cleanPhone.startsWith('0') && cleanPhone.length === 11) {
            cleanPhone = '91' + cleanPhone.substring(1);
        } else if (cleanPhone.startsWith('91') && cleanPhone.length > 12) {
            cleanPhone = cleanPhone.substring(0, 12);
        }
        
        const message = `Hello ${patientName},

Your appointment has been confirmed for ${appointmentDate} at ${appointmentTime}.

ClinicFlow
Thank you!`;
        
        // Send WhatsApp notification using api.whatsapp.com
        const whatsappUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(message)}`;
        
        // Optional: Auto-open WhatsApp (or show notification)
        if (confirm('Send WhatsApp confirmation to patient?')) {
            window.open(whatsappUrl, '_blank');
        }
    }
}
</script>
@endsection
