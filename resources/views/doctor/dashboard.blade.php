@extends('layouts.app')

@section('title', 'Doctor Dashboard - ClinicFlow')

@section('styles')
<style>
    /* Design System Variables */
    :root {
        --color-black: #1a1a1a;
        --color-white: #ffffff;
        --color-gray-50: #f9fafb;
        --color-gray-100: #f3f4f6;
        --color-gray-200: #e5e7eb;
        --color-gray-300: #d1d5db;
        --color-gray-400: #9ca3af;
        --color-gray-500: #6b7280;
        --color-gray-600: #4b5563;
        --color-gray-700: #374151;
    }
    
    .dash-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; height: calc(100vh - var(--header-height) - 2.5rem); }
    .main-col { display: flex; flex-direction: column; gap: 1rem; min-height: 0; }
    .page-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-sub { color: var(--color-gray-500); font-size: 0.8rem; margin: 0; }
    
    /* Modern Stat Cards */
    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1rem; }
    .stat-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; text-decoration: none; color: inherit; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .stat-icon-box { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon-box i { font-size: 1.5rem; }
    .stat-icon-box.black { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: var(--color-white); }
    .stat-icon-box.green { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: var(--color-white); }
    .stat-icon-box.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: var(--color-white); }
    .stat-icon-box.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: var(--color-white); }
    .stat-content h3 { font-size: 1.75rem; font-weight: 700; margin: 0; color: var(--color-black); line-height: 1; }
    .stat-content p { font-size: 0.75rem; color: var(--color-gray-500); margin: 0.25rem 0 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .queue-card { flex: 1; background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); display: flex; flex-direction: column; min-height: 0; overflow: hidden; box-shadow: var(--shadow-sm); }
    .queue-card, .queue-card:hover, .queue-card:focus, .queue-card:focus-within, .queue-card:active { transition: none !important; transform: none !important; box-shadow: var(--shadow-sm) !important; outline: none !important; }
    .queue-card .header { display: flex; justify-content: space-between; align-items: center; padding: 0.875rem 1rem; border-bottom: 1px solid var(--color-gray-200); background: var(--color-gray-50); }
    .queue-card .header h3 { font-size: 0.9rem; font-weight: 600; margin: 0; }
    .queue-card .body { flex: 1; overflow-y: auto; }
    .queue-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; border-bottom: 1px solid var(--color-gray-100); cursor: pointer; transition: all var(--transition-fast); }
    .queue-item:hover { background: var(--color-gray-50); padding-left: 1.25rem; }
    .queue-num { background: linear-gradient(135deg, var(--color-black) 0%, #333 100%); color: var(--color-white); font-size: 0.75rem; font-weight: 600; padding: 0.35rem 0.5rem; border-radius: var(--radius-sm); min-width: 32px; text-align: center; }
    .queue-info { flex: 1; }
    .queue-info h6 { font-size: 0.875rem; font-weight: 600; margin: 0 0 0.25rem; }
    .queue-meta { display: flex; flex-wrap: wrap; gap: 0.5rem; font-size: 0.7rem; color: var(--color-gray-500); }
    .queue-meta span { display: flex; align-items: center; gap: 0.25rem; }
    .status-pill { font-size: 0.65rem; font-weight: 600; padding: 0.25rem 0.5rem; text-transform: uppercase; border-radius: var(--radius-sm); }
    .status-pill.waiting { background: var(--color-gray-200); color: var(--color-gray-700); }
    .status-pill.in-consult { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; }
    .status-pill.done { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #15803d; }
    .empty-queue { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1rem; color: var(--color-gray-400); text-align: center; }
    .empty-queue i { font-size: 2.5rem; opacity: 0.3; margin-bottom: 0.75rem; }
    .empty-queue h5 { font-size: 0.9rem; color: var(--color-gray-600); margin-bottom: 0.25rem; }
    .side-col { display: flex; flex-direction: column; gap: 0.75rem; }
    .clinic-info { background: linear-gradient(135deg, var(--color-black) 0%, #2d2d2d 100%); color: var(--color-white); padding: 1.25rem; text-align: center; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
    .clinic-info .logo { width: 56px; height: 56px; background: rgba(255,255,255,0.1); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; overflow: hidden; }
    .clinic-info .logo img { max-width: 48px; max-height: 48px; border-radius: var(--radius-md); }
    .clinic-info .logo i { font-size: 1.5rem; opacity: 0.5; }
    .clinic-info h4 { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
    .clinic-info .doctor { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-bottom: 0.75rem; }
    .clinic-info .fee-box { background: rgba(255,255,255,0.1); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 0.75rem; }
    .clinic-info .fee-label { font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.6); letter-spacing: 0.5px; }
    .clinic-info .fee-amount { font-size: 1.25rem; font-weight: 700; }
    .actions-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); flex: 1; overflow: hidden; box-shadow: var(--shadow-sm); }
    .actions-card .header { padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-gray-200); font-size: 0.8rem; font-weight: 600; background: var(--color-gray-50); display: flex; align-items: center; gap: 0.5rem; }
    .action-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; text-decoration: none; color: inherit; border-bottom: 1px solid var(--color-gray-100); transition: all var(--transition-fast); }
    .action-link:last-child { border-bottom: none; }
    .action-link:hover { background: var(--color-gray-50); padding-left: 1.25rem; }
    .action-link .icon { width: 32px; height: 32px; background: var(--color-gray-100); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: var(--color-gray-600); transition: all var(--transition-fast); }
    .action-link:hover .icon { background: var(--color-black); color: var(--color-white); }
    .action-link h6 { font-size: 0.8rem; font-weight: 600; color: var(--color-black); margin: 0; }
    .action-link small { font-size: 0.7rem; color: var(--color-gray-500); }
    /* Appointments Widget */
    .appointments-widget { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); margin-bottom: 0.75rem; }
    .appointments-widget .header { padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-gray-200); font-size: 0.8rem; font-weight: 600; background: var(--color-gray-50); display: flex; justify-content: space-between; align-items: center; }
    .appointment-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; border-bottom: 1px solid var(--color-gray-100); font-size: 0.75rem; }
    .appointment-item:last-child { border-bottom: none; }
    .appointment-item .time { font-weight: 600; color: var(--color-info); min-width: 55px; }
    .appointment-item .name { flex: 1; font-weight: 500; }
    .appointment-item .status { font-size: 0.6rem; padding: 0.15rem 0.4rem; border-radius: var(--radius-sm); text-transform: uppercase; font-weight: 600; }
    .appointment-item .status.scheduled { background: #dbeafe; color: #1e40af; }
    .appointment-item .status.confirmed { background: #dcfce7; color: #166534; }
    /* Alerts Widget */
    .alerts-widget { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); }
    .alerts-widget .header { padding: 0.75rem 1rem; border-bottom: 1px solid var(--color-gray-200); font-size: 0.8rem; font-weight: 600; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #b91c1c; display: flex; justify-content: space-between; align-items: center; }
    .alert-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-bottom: 1px solid var(--color-gray-100); font-size: 0.75rem; }
    .alert-item:last-child { border-bottom: none; }
    .alert-item .icon { font-size: 0.9rem; }
    .alert-item .icon.low { color: var(--color-warning); }
    .alert-item .icon.critical { color: var(--color-danger); }
    .alert-item .icon.expiring { color: var(--color-info); }
    .alert-item .text { flex: 1; }
    .alert-item .badge { font-size: 0.6rem; padding: 0.15rem 0.35rem; }
    @media (max-width: 1024px) { .dash-grid { grid-template-columns: 1fr; height: auto; } .side-col { flex-direction: row; flex-wrap: wrap; } .clinic-info { flex: 1; min-width: 200px; } .actions-card { flex: 2; min-width: 300px; } .queue-card { min-height: 350px; } .appointments-widget, .alerts-widget { flex: 1; min-width: 250px; } .stat-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stat-row { grid-template-columns: repeat(2, 1fr); } .side-col { flex-direction: column; } }
    @media (max-width: 480px) { .stat-row { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<!-- CACHE BUSTER: {{ now()->timestamp }} v2.0 -->
<div class="dash-grid">
    <div class="main-col">
        <div>
            <h1 class="page-title"><i class="bi bi-speedometer2"></i> Doctor Dashboard</h1>
            <p class="page-sub">@if($doctorProfile && $doctorProfile->clinic_name){{ $doctorProfile->clinic_name }} • @endif{{ auth()->user()->name }} • {{ now()->format('l, F j, Y') }}</p>
        </div>
        
        <!-- New Stat Cards -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-icon-box black">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count($todayQueue) }}</h3>
                    <p>Today's Queue</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $completedCount }}</h3>
                    <p>Completed</p>
                </div>
            </div>
            <a href="{{ route('doctor.appointments') }}" class="stat-card">
                <div class="stat-icon-box purple">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $todayAppointments ?? 0 }}</h3>
                    <p>Today's Appts</p>
                </div>
            </a>
            <a href="{{ route('doctor.inventory-alerts') }}" class="stat-card">
                <div class="stat-icon-box red">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $alertCount ?? 0 }}</h3>
                    <p>Alerts</p>
                </div>
            </a>
        </div>
        
        <div class="queue-card">
            <div class="header"><h3><i class="bi bi-list-task"></i> Patient Queue</h3><button class="btn btn-sm btn-dark" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i> Refresh</button></div>
            <div class="body">
                @if(count($todayQueue) > 0)
                    @foreach($todayQueue as $visit)
                    @if($visit->patient)
                    <div class="queue-item" id="queue-item-{{ $visit->id }}">
                        <span class="queue-num">#{{ $visit->queue_number }}</span>
                        <div class="queue-info" onclick="viewPatient({{ $visit->id }})" style="cursor:pointer;">
                            <h6>{{ $visit->patient->name ?? 'Unknown Patient' }}</h6>
                            <div class="queue-meta">
                                @if(isset($visit->patient->age) && $visit->patient->age)<span><i class="bi bi-person"></i> {{ $visit->patient->age }}y</span>@endif
                                @if(isset($visit->patient->gender) && $visit->patient->gender)<span><i class="bi bi-gender-ambiguous"></i> {{ ucfirst($visit->patient->gender) }}</span>@endif
                                @if(isset($visit->patient->phone) && $visit->patient->phone)<span><i class="bi bi-telephone"></i> {{ $visit->patient->phone }}</span>@endif
                                <span><i class="bi bi-clock"></i> {{ $visit->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="queue-actions" style="display:flex;gap:0.5rem;align-items:center;">
                            @if($visit->status === 'in_consult')<span class="status-pill in-consult">In Consult</span>@elseif($visit->status === 'completed')<span class="status-pill done">Done</span>@else<span class="status-pill waiting">Waiting</span>@endif
                            <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteFromQueue({{ $visit->id }})" title="Remove from queue" style="padding:0.2rem 0.4rem;"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    @endif
                    @endforeach
                @else
                    <div class="empty-queue"><i class="bi bi-inbox"></i><h5>No patients in queue</h5><p>Patients will appear here when they check in</p></div>
                @endif
            </div>
        </div>
    </div>
    <div class="side-col">
        <div class="clinic-info">
            <div class="logo">
                @if($doctorProfile && method_exists($doctorProfile, 'getClinicLogoUrl') && $doctorProfile->getClinicLogoUrl())
                    <img src="{{ $doctorProfile->getClinicLogoUrl() }}" alt="Logo">
                @else
                    <i class="bi bi-hospital"></i>
                @endif
            </div>
            <h4>{{ $doctorProfile->clinic_name ?? 'My Clinic' }}</h4>
            <p class="doctor">Dr. {{ auth()->user()->name }}</p>
            @if($doctorProfile && $doctorProfile->default_doctor_fee)
                <div class="fee-box">
                    <div class="fee-label">DEFAULT FEE</div>
                    <div class="fee-amount">₹{{ number_format($doctorProfile->default_doctor_fee, 2) }}</div>
                </div>
            @endif
            <a href="{{ route('doctor.clinic.settings') }}" class="btn btn-outline-light btn-sm w-100"><i class="bi bi-pencil"></i> Edit Settings</a>
        </div>
        
        <!-- Upcoming Appointments Widget -->
        <div class="appointments-widget">
            <div class="header">
                <span><i class="bi bi-calendar-check"></i> Today's Appointments</span>
                <a href="{{ route('doctor.appointments') }}" class="btn btn-sm btn-outline-dark" style="padding:0.2rem 0.5rem;font-size:0.65rem;">View All</a>
            </div>
            @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
                @foreach($upcomingAppointments->take(4) as $apt)
                <div class="appointment-item">
                    <span class="time">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</span>
                    <span class="name">{{ $apt->patient->name ?? 'Unknown' }}</span>
                    <span class="status {{ $apt->status }}">{{ $apt->status }}</span>
                </div>
                @endforeach
            @else
                <div class="p-3 text-center text-muted" style="font-size:0.75rem;">
                    <i class="bi bi-calendar-x"></i> No appointments today
                </div>
            @endif
        </div>
        
        <!-- Inventory Alerts Widget -->
        @if(isset($inventoryAlerts) && count($inventoryAlerts) > 0)
        <div class="alerts-widget">
            <div class="header">
                <span><i class="bi bi-exclamation-triangle"></i> Inventory Alerts</span>
                <span class="badge bg-danger">{{ count($inventoryAlerts) }}</span>
            </div>
            @foreach(collect($inventoryAlerts)->take(3) as $alert)
            <div class="alert-item">
                <i class="bi bi-{{ $alert['status'] === 'expiring' ? 'calendar-x' : 'box' }} icon {{ $alert['status'] }}"></i>
                <span class="text">{{ $alert['name'] }}</span>
                <span class="badge bg-{{ $alert['status'] === 'critical' ? 'danger' : ($alert['status'] === 'low' ? 'warning' : 'info') }}">
                    {{ $alert['status'] === 'expiring' ? 'Expiring' : $alert['stock'] . ' left' }}
                </span>
            </div>
            @endforeach
            <a href="{{ route('doctor.inventory-alerts') }}" class="d-block text-center py-2" style="font-size:0.7rem;text-decoration:none;border-top:1px solid var(--color-gray-100);">View All Alerts →</a>
        </div>
        @endif
        
        <div class="actions-card">
            <div class="header"><i class="bi bi-lightning-charge"></i> Quick Actions</div>
            <a href="{{ route('doctor.schedule') }}" class="action-link"><div class="icon"><i class="bi bi-clock"></i></div><div><h6>Schedule</h6><small>Manage timing</small></div></a>
            <a href="{{ route('doctor.appointments') }}" class="action-link"><div class="icon"><i class="bi bi-calendar-plus"></i></div><div><h6>Appointments</h6><small>Schedule & manage</small></div></a>
            <a href="{{ route('doctor.reports') }}" class="action-link"><div class="icon"><i class="bi bi-graph-up"></i></div><div><h6>Reports</h6><small>Analytics & insights</small></div></a>
            <a href="{{ route('doctor.clinic.settings') }}" class="action-link"><div class="icon"><i class="bi bi-gear"></i></div><div><h6>Clinic Settings</h6><small>Manage clinic info</small></div></a>
            <a href="{{ route('doctor.charges.list') }}" class="action-link"><div class="icon"><i class="bi bi-currency-rupee"></i></div><div><h6>Service Charges</h6><small>Manage pricing</small></div></a>
            <a href="{{ route('doctor.receptions') }}" class="action-link"><div class="icon"><i class="bi bi-people"></i></div><div><h6>Reception Staff</h6><small>Manage team</small></div></a>
            <a href="{{ route('doctor.medicines') }}" class="action-link"><div class="icon"><i class="bi bi-capsule"></i></div><div><h6>Medicines</h6><small>Inventory</small></div></a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function viewPatient(visitId) { window.location.href = '/doctor/visit/' + visitId; }

// Delete from queue
async function deleteFromQueue(visitId) {
    if (!confirm('Remove this patient from the queue? This will cancel their visit.')) return;
    
    try {
        const res = await axios.delete('/api/queue/' + visitId);
        if (res.data.success) {
            document.getElementById('queue-item-' + visitId)?.remove();
            if (window.showGlobalNotification) {
                window.showGlobalNotification(res.data.message || 'Removed from queue', 'success');
            }
            // Refresh stats
            setTimeout(() => location.reload(), 1000);
        }
    } catch (e) {
        if (window.showGlobalNotification) {
            window.showGlobalNotification(e.response?.data?.message || 'Error removing from queue', 'error');
        } else {
            alert(e.response?.data?.message || 'Error removing from queue');
        }
    }
}

// Real-time Patient Queue update (AJAX polling)
function renderQueue(queue) {
    const queueBody = document.querySelector('.queue-card .body');
    if (!queueBody) return;
    if (queue.length === 0) {
        queueBody.innerHTML = '<div class="empty-queue"><i class="bi bi-inbox"></i><h5>No patients in queue</h5><p>Patients will appear here when they check in</p></div>';
        return;
    }
    queueBody.innerHTML = queue.map(visit => `
        <div class="queue-item" id="queue-item-${visit.id}">
            <span class="queue-num">#${visit.queue_number}</span>
            <div class="queue-info" onclick="viewPatient(${visit.id})" style="cursor:pointer;">
                <h6>${visit.patient_name}</h6>
                <div class="queue-meta">
                    ${visit.patient_age ? `<span><i class='bi bi-person'></i> ${visit.patient_age}y</span>` : ''}
                    ${visit.patient_gender ? `<span><i class='bi bi-gender-ambiguous'></i> ${visit.patient_gender.charAt(0).toUpperCase()}</span>` : ''}
                    ${visit.patient_phone ? `<span><i class='bi bi-telephone'></i> ${visit.patient_phone}</span>` : ''}
                    <span><i class='bi bi-clock'></i> ${visit.enqueued_at ? (new Date(visit.enqueued_at)).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                </div>
            </div>
            <div class="queue-actions" style="display:flex;gap:0.5rem;align-items:center;">
                ${visit.status === 'in_consult' ? '<span class="status-pill in-consult">In Consult</span>' : visit.status === 'completed' ? '<span class="status-pill done">Done</span>' : '<span class="status-pill waiting">Waiting</span>'}
                <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteFromQueue(${visit.id})" title="Remove from queue" style="padding:0.2rem 0.4rem;"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    `).join('');
}

async function fetchQueue() {
    try {
        const res = await axios.get('/api/queue');
        if (res.data.success) {
            renderQueue(res.data.queue.map(q => ({
                ...q,
                patient_age: q.patient_age || '',
                patient_gender: q.patient_gender || '',
            })));
        }
    } catch (e) {
        // Optionally show error
    }
}

setInterval(fetchQueue, 10000); // Poll every 10s
fetchQueue();
</script>
@endsection
