@extends('layouts.app')

@section('title', 'Reception Dashboard - ClinicFlow')

@section('styles')
<!-- Cache Buster: {{ now()->timestamp }} -->
<style>
    /* Transparency Fix: Define Colors explicitly */
    /* Cache Version: v8.0-modal-fix */
    :root {
        --color-black: #1a1a1a;
        --color-white: #ffffff;
        --color-gray-50: #f9fafb;
        --color-gray-100: #f3f4f6;
        --color-gray-200: #e5e7eb;
        --color-gray-300: #d1d5db;
        --color-gray-400: #9ca3af;
        --color-gray-500: #6b7280;
    }

    .reception-title { font-size: 1.1875rem; font-weight: 700; margin-bottom: 0.25rem; }
    .reception-sub { color: var(--color-gray-500); font-size: 0.75rem; }
    
    /* Redesigned Stats */
    .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
    .stat-box { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; }
    .stat-box:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .stat-box.black { background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); border-color: transparent; }
    .stat-box.black .stat-info .value { color: #fff; }
    .stat-box.black .stat-info .label { color: rgba(255,255,255,0.7); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .stat-icon.queue { background: rgba(255,255,255,0.15); color: #fff; }
    .stat-icon.waiting { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; }
    .stat-icon.consult { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; }
    .stat-icon.completed { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff; }
    .stat-info { flex: 1; }
    .stat-info .value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: #1a1a1a; }
    .stat-info .label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 500; }
    
    .queue-section { display: grid; grid-template-columns: 1fr 280px; gap: 1rem; height: calc(100vh - var(--header-height) - 10rem); }
    .queue-card { background: #ffffff; /* Fallback */ background: var(--color-white); border: 1px solid var(--color-gray-200); display: flex; flex-direction: column; overflow: hidden; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .queue-card, .queue-card:hover, .queue-card:focus, .queue-card:focus-within, .queue-card:active { transition: none !important; transform: none !important; box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important; outline: none !important; }
    .queue-card .header { padding: 0.625rem 0.75rem; border-bottom: 1px solid var(--color-gray-200); font-weight: 600; font-size: 0.875rem; display: flex; justify-content: space-between; align-items: center; }
    .queue-body { flex: 1; overflow-y: auto; }
    .queue-item { padding: 0.75rem 0.75rem; border-bottom: 1px solid var(--color-gray-100); cursor: pointer; transition: background 0.15s ease; }
    .queue-item:hover { background: var(--color-gray-50); }
    .queue-num { background: var(--color-black); color: var(--color-white); font-size: 0.7rem; font-weight: 600; padding: 0.25rem 0.4rem; display: inline-block; border-radius: 4px; }
    .queue-name { font-size: 0.8125rem; font-weight: 600; margin: 0.25rem 0 0.125rem; }
    .queue-meta { font-size: 0.65rem; color: var(--color-gray-500); }
    .queue-meta span { margin-right: 0.5rem; }
    .status-badge { font-size: 0.6rem; font-weight: 600; padding: 0.2rem 0.4rem; text-transform: uppercase; border-radius: 4px; }
    .status-waiting { background: var(--color-gray-200); color: var(--color-gray-700); }
    .status-consult { background: #dbeafe; color: #1d4ed8; }
    .status-completed { background: #dcfce7; color: #15803d; }
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; color: var(--color-gray-400); text-align: center; }
    .empty-state i { font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem; }
    
    /* Explicit Black Background for Register Form */
    .register-form { background: #1a1a1a !important; color: #ffffff !important; border-radius: 12px; }
    
    .register-form .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff; border-radius: 6px; transition: all 0.2s ease; }
    .register-form .form-control:focus { background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); }
    .register-form .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .register-form .form-label { color: rgba(255,255,255,0.9); font-size: 0.65rem; font-weight: 600; }
    
    .side-actions { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .action-item { padding: 0.625rem 0.75rem; border-bottom: 1px solid var(--color-gray-100); display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-decoration: none; color: inherit; transition: background 0.15s ease; }
    .action-item:first-child { border-radius: 10px 10px 0 0; }
    .action-item:last-child { border-bottom: none; border-radius: 0 0 10px 10px; }
    .action-item:hover { background: var(--color-gray-50); }
    .action-icon { width: 24px; height: 24px; background: var(--color-gray-100); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; border-radius: 6px; }
    .action-text h6 { font-size: 0.75rem; font-weight: 600; margin: 0; }
    .action-text small { font-size: 0.65rem; color: var(--color-gray-500); }
    .alert { border-radius: 8px; }
    
    /* Autocomplete List Styling */
    #existing-autocomplete-list {
        background: #ffffff !important;
        border: 1px solid #ddd;
        z-index: 9999;
    }
    
    /* Modal Styling Fix - Force proper layering */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.75) !important;
        backdrop-filter: blur(4px);
        z-index: 1040 !important;
    }
    
    .modal-backdrop:not(.show) {
        display: none !important;
    }
    
    .modal {
        z-index: 9999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 4rem 1rem !important;
    }
    
    .modal:not(.show) {
        display: none !important;
    }
    
    .modal.show {
        display: flex !important;
    }
    
        .modal-dialog {
            z-index: 10000 !important;
            margin: 1.5rem auto !important;
            max-width: 540px !important;
            width: calc(100% - 2rem) !important;
            height: auto !important;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 3rem) !important;
        }

        .modal-content {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 20px !important;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.28) !important;
            overflow: hidden !important;
            display: block !important;
            width: 100% !important;
            height: auto !important;
        }

        .queue-modal-header {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%) !important;
            color: #fff !important;
            padding: 1rem 1.25rem !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }

        .queue-modal-title {
            display: flex !important;
            align-items: center !important;
            gap: 0.6rem !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
            color: #ffffff !important;
        }

        .queue-modal-title i {
            font-size: 1.1rem;
        }

        .queue-modal-close {
            width: 36px !important;
            height: 36px !important;
            border: none !important;
            border-radius: 10px !important;
            background: rgba(255,255,255,0.12) !important;
            color: #fff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }

        .queue-modal-close:hover {
            background: rgba(255,255,255,0.2) !important;
        }

        .queue-modal-body {
            padding: 1.1rem 1.25rem 1.25rem !important;
            background: #ffffff !important;
        }

        .queue-modal-panel {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.9rem !important;
        }

        .queue-search-label {
            display: flex !important;
            align-items: center !important;
            gap: 0.45rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            font-size: 0.86rem !important;
            margin: 0 !important;
        }

        .queue-input {
            width: 100% !important;
            padding: 0.85rem 0.95rem !important;
            border: 1.5px solid #d1d5db !important;
            border-radius: 12px !important;
            font-size: 0.92rem !important;
            background: #fff !important;
            color: #111827 !important;
            box-shadow: none !important;
        }

        .queue-input:focus {
            border-color: #111827 !important;
            box-shadow: 0 0 0 3px rgba(17,24,39,0.08) !important;
            outline: none !important;
        }

        .queue-autocomplete {
            display: none;
            z-index: 1000;
            max-height: 220px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            margin-top: 0.35rem !important;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }

        .queue-result-card {
            display: none;
            background: #f8fafc;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 0.95rem 1rem;
        }

        .queue-alert-card {
            display: none;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 0.95rem 1rem;
        }

        .queue-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 0.15rem;
        }

        .queue-btn {
            width: 100%;
            border-radius: 12px !important;
            padding: 0.78rem 1rem !important;
            font-weight: 700 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.55rem !important;
            border: 1px solid transparent !important;
        }

        .queue-btn-secondary {
            background: #ffffff !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }

        .queue-btn-secondary:hover {
            background: #f9fafb !important;
        }

        .queue-btn-primary {
            background: linear-gradient(135deg, #111827 0%, #374151 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 24px rgba(17,24,39,0.18) !important;
        }

        .queue-btn-primary:disabled {
            background: #9ca3af !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }

        .queue-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #6b7280;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 0.1rem 0;
        }

        .queue-divider::before,
        .queue-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .queue-register-link {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 24px rgba(16,185,129,0.18) !important;
            text-decoration: none !important;
        }

        .queue-register-link:hover {
            color: #ffffff !important;
        }

        #modal-autocomplete-list .list-group-item {
            border: none;
            border-bottom: 1px solid #f3f4f6;
            padding: 0.75rem;
            cursor: pointer;
            background: #ffffff;
            color: #1a1a1a;
        }

        #modal-autocomplete-list .list-group-item:hover {
            background: #f9fafb;
        }

        #modal-autocomplete-list .list-group-item:last-child {
            border-bottom: none;
        }
    
    @media (max-width: 1024px) { .queue-section { grid-template-columns: 1fr; height: auto; } .stats-container { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-container { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<!-- Cache Buster: {{ now()->timestamp }} -->
<div style="margin-bottom: 1rem;">
    <h1 class="reception-title"><i class="bi bi-grid"></i> Reception Dashboard</h1>
    <p class="reception-sub">{{ auth()->user()->name }} • {{ now()->format('l, F j, Y') }}</p>
    <div style="font-size: 0.65rem; color: #9ca3af;">Build v7.2 • {{ now()->format('H:i:s') }}</div>
</div>

@if(count($lowStockMedicines) > 0)
<div class="alert alert-danger" style="margin-bottom: 1rem;">
    <strong>⚠ Low Stock Alert:</strong> {{ count($lowStockMedicines) }} medicine(s) need restocking
</div>
@endif

<div class="stats-container">
    <div class="stat-box black">
        <div class="stat-icon queue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="value">{{ count($todayVisits) }}</div>
            <div class="label">Total Queue</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon waiting"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayVisits->where('status', 'waiting')->count() }}</div>
            <div class="label">Waiting</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon consult"><i class="bi bi-activity"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayVisits->where('status', 'in_consult')->count() }}</div>
            <div class="label">In Consult</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon completed"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayVisits->where('status', 'completed')->count() }}</div>
            <div class="label">Completed</div>
        </div>
    </div>
</div>

<div class="queue-section">
    <div class="queue-card">
        <div class="header"><h3 style="margin: 0; font-size: 0.875rem;"><i class="bi bi-list-ol"></i> Patient Queue</h3><button class="btn btn-sm btn-dark" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i></button></div>
        <div class="queue-body">
            @if(count($todayVisits) > 0)
                @foreach($todayVisits as $visit)
                @if($visit->patient)
                <div class="queue-item" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="flex: 1;">
                        <div class="queue-name">{{ $visit->patient->name }}</div>
                        <div class="queue-meta">
                            @if($visit->patient->age)<span><i class="bi bi-person"></i> {{ $visit->patient->age }}y</span>@endif
                            @if($visit->patient->gender)<span><i class="bi bi-gender-ambiguous"></i> {{ substr(ucfirst($visit->patient->gender), 0, 1) }}</span>@endif
                            <span><i class="bi bi-clock"></i> {{ $visit->created_at->format('H:i') }}</span>
                        </div>
                        <div style="margin-top: 0.25rem;">
                            @if($visit->status === 'in_consult')
                                <span class="status-badge status-consult">In Consult</span>
                            @elseif($visit->status === 'completed')
                                <span class="status-badge status-completed">Done</span>
                            @else
                                <span class="status-badge status-waiting">Waiting</span>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        @if($visit->status === 'waiting')
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFromQueue({{ $visit->id }})" title="Remove from queue" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                        <span class="queue-num">#{{ $visit->queue_number }}</span>
                    </div>
                </div>
                @endif
                @endforeach
            @else
                <div class="empty-state"><i class="bi bi-inbox"></i><h5>No patients in queue</h5></div>
            @endif
        </div>
    </div>


    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <!-- Patient Action Buttons -->
        <div class="queue-card">
            <div class="header" style="background: var(--color-black); border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--color-white); padding: 0.625rem 0.75rem;">
                <h3 style="margin: 0; font-size: 0.875rem;"><i class="bi bi-person-plus"></i> Add Patient to Queue</h3>
            </div>
            <div style="padding: 1rem; display: flex; gap: 0.75rem; flex-direction: column;">
                <!-- Existing Patient Button (Using Bootstrap Modal) -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#existingPatientModal" style="padding: 1rem; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none;">
                    <i class="bi bi-person-check" style="font-size: 1.5rem;"></i>
                    <div style="text-align: left;">
                        <div style="font-weight: 600;">Existing Patient</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Search and add to queue</div>
                    </div>
                </button>

                <!-- New Patient Button (Direct Link) -->
                <a href="{{ route('reception.patients') }}" class="btn btn-success" style="padding: 1rem; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; text-decoration: none;">
                    <i class="bi bi-person-plus-fill" style="font-size: 1.5rem;"></i>
                    <div style="text-align: left;">
                        <div style="font-weight: 600;">Register New Patient</div>
                        <div style="font-size: 0.75rem; opacity: 0.9;">Go to Patients page</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today's Appointments Section -->
        <div class="queue-card">
            <div class="header" style="background: #dbeafe;">
                <h3 style="margin: 0; font-size: 0.875rem; color: #1e40af;"><i class="bi bi-calendar-event"></i> Today's Appointments</h3>
                <a href="{{ route('reception.appointments') }}" class="btn btn-sm btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.65rem;">View All</a>
            </div>
            <div class="queue-body" style="overflow-y: auto; max-height: 300px;">
                @if(isset($todayAppointments) && count($todayAppointments) > 0)
                    @foreach($todayAppointments as $apt)
                    <div class="queue-item" style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <div class="queue-name">{{ $apt->patient->name ?? 'Unknown' }}</div>
                            <div class="queue-meta">
                                <span><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</span>
                                @if($apt->reason)<span>• {{ Str::limit($apt->reason, 20) }}</span>@endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.25rem;">
                            <!-- Using standard POST form instead of JS function -->
                            <form action="{{ route('reception.enqueue') }}" method="POST" onsubmit="return confirm('Add to queue?')">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $apt->patient_id }}">
                                <button type="submit" class="btn btn-sm btn-success" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">
                                    <i class="bi bi-plus"></i> Queue
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state" style="padding: 1.5rem;"><i class="bi bi-calendar-x"></i><p style="font-size: 0.75rem; margin: 0.5rem 0 0;">No appointments today</p></div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal for Existing Patient -->
<div class="modal fade" id="existingPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="queue-modal-header">
                <h5 class="queue-modal-title">
                    <i class="bi bi-person-check"></i>
                    Add to Queue
                </h5>
                <button type="button" class="btn-close queue-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="queue-modal-body">
                <form id="existing-enqueue-form-modal" method="POST" action="{{ route('reception.enqueue') }}">
                    @csrf
                    <input type="hidden" id="modal_patient_id" name="patient_id">

                    <div class="queue-modal-panel">
                        <div class="position-relative">
                            <label class="queue-search-label" for="modal_patient_search">
                                <i class="bi bi-search"></i>
                                Search Patient
                            </label>
                            <input type="text" class="queue-input" id="modal_patient_search" placeholder="Type patient name..." autocomplete="off">
                            <div id="modal-autocomplete-list" class="queue-autocomplete list-group position-absolute w-100"></div>
                        </div>

                        <div id="selected-patient-info" class="queue-result-card">
                            <div class="fw-bold text-slate-800 d-flex align-items-center gap-2" style="font-size: 0.86rem; color: #0f172a;">
                                <i class="bi bi-person-check-fill"></i>
                                Selected Patient
                            </div>
                            <div id="display_name" class="mt-2 fw-semibold" style="font-size: 0.95rem; color: #111827;"></div>
                            <small style="display: block; margin-top: 0.35rem; color: #0891b2; font-size: 0.78rem;">
                                ID: <span id="display_id"></span> | Age: <span id="display_age"></span>
                            </small>
                        </div>

                        <div id="patient-not-found" class="queue-alert-card">
                            <div class="fw-bold d-flex align-items-center gap-2" style="color: #9a3412; font-size: 0.86rem;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Patient not found?
                            </div>
                            <p class="mb-0 mt-2" style="font-size: 0.8rem; color: #9a3412;">Please register the patient first.</p>
                        </div>

                        <div class="queue-actions">
                            <button type="button" class="btn queue-btn queue-btn-secondary" onclick="document.getElementById('modal_patient_search').value=''; document.getElementById('modal_patient_id').value=''; document.getElementById('selected-patient-info').style.display='none'; document.getElementById('patient-not-found').style.display='none'; document.getElementById('btn-add-queue').disabled=true;">
                                <i class="bi bi-x-lg"></i>
                                Clear
                            </button>

                            <button type="submit" class="btn queue-btn queue-btn-primary" id="btn-add-queue" disabled>
                                <i class="bi bi-check-lg"></i>
                                Add to Queue
                            </button>

                            <div class="queue-divider">or</div>

                            <a href="{{ route('reception.patients') }}" class="btn queue-btn queue-register-link">
                                <i class="bi bi-person-plus-fill"></i>
                                Register New Patient
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    {{-- External dashboard logic (v7.1) --}}
    <script src="{{ asset('js/reception-dashboard.js') }}?v={{ config('app.version') }}"></script>
@endsection


