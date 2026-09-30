@extends('layouts.app')

@section('title', 'Patients - Reception - ClinicFlow')

@section('styles')
<style>
    /* Redesigned Stats */
    .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
    .stat-box { background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-box:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.12); transform: translateY(-3px); border-color: #d1d5db; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .stat-icon.total { background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); color: #fff; }
    .stat-icon.today { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; }
    .stat-icon.male { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: #fff; }
    .stat-icon.female { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: #fff; }
    .stat-info { flex: 1; }
    .stat-info .value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: #1a1a1a; }
    .stat-info .label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 500; }
    
    .patients-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .search-box { display: flex; gap: 0.5rem; flex: 1; max-width: 400px; }
    .search-box input { flex: 1; padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; background: var(--color-white); color: var(--color-black); border-radius: 10px; transition: all 0.2s ease; }
    .search-box input:focus { border-color: #1a1a1a; outline: none; box-shadow: 0 0 0 3px rgba(26,26,26,0.1); }
    .patients-table { width: 100%; background: var(--color-white); border: 2px solid #e5e7eb; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .patients-table thead th { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #ffffff; padding: 0.875rem; border-bottom: 1px solid var(--color-gray-200); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .patients-table tbody td { padding: 0.875rem 0.75rem; border-bottom: 1px solid var(--color-gray-100); font-size: 0.8rem; }
    .patients-table tbody tr:hover { background: var(--color-gray-50); }
    .patients-table tbody tr:last-child td { border-bottom: none; }
    
    .action-btn { padding: 0.4rem 0.7rem; margin: 0 0.25rem; font-size: 0.75rem; border: 2px solid transparent; cursor: pointer; border-radius: 8px; font-weight: 600; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.25rem; }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
    .action-btn.primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; border-color: #2563eb; }
    .action-btn.primary:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-color: #1d4ed8; box-shadow: 0 4px 12px rgba(37,99,235,0.4); }
    .action-btn.secondary { background: #ffffff; color: #1a1a1a; border-color: #d1d5db; }
    .action-btn.secondary:hover { background: #f9fafb; border-color: #9ca3af; }
    .action-btn.danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; border-color: #fca5a5; font-weight: 700; }
    .action-btn.danger:hover { background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); border-color: #f87171; box-shadow: 0 4px 12px rgba(220,38,38,0.3); }
    .pagination { display: flex; gap: 0.5rem; margin-top: 1rem; justify-content: center; }
    .pagination a, .pagination span { padding: 0.5rem 0.75rem; border: 2px solid #e5e7eb; font-size: 0.8rem; border-radius: 8px; font-weight: 600; transition: all 0.2s ease; }
    .pagination a:hover { border-color: #1a1a1a; background: #f9fafb; transform: translateY(-1px); }
    .pagination .active { background: var(--color-black); color: var(--color-white); border-color: var(--color-black); }
    .empty-state { text-align: center; padding: 2rem; color: var(--color-gray-400); }
    /* Custom Modal - Professional Design */
    .custom-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 4rem 1rem; }
    .custom-modal.show { display: flex; }
    .custom-modal-dialog { background: #ffffff; border-radius: 16px; width: 100%; max-width: 600px; max-height: 100%; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 3px solid #e5e7eb; margin: auto; }
    .custom-modal-header { flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: #ffffff; }
    .custom-modal-header h5 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .custom-modal-header h5 i { font-size: 1.25rem; }
    .custom-modal-header .btn-close { background: rgba(255,255,255,0.1) !important; border: none !important; cursor: pointer; color: white !important; width: 32px; height: 32px; display: flex !important; align-items: center; justify-content: center; border-radius: 8px; opacity: 1 !important; transition: all 0.2s ease; line-height: 1; margin: 0; padding: 0; }
    .custom-modal-header .btn-close:hover { background: rgba(255,255,255,0.25) !important; transform: rotate(90deg); }
    .custom-modal-header .btn-close::before { content: "×"; font-size: 1.75rem; line-height: 1; display: block; }
    .custom-modal-body { flex: 1; overflow-y: auto; padding: 1.5rem; background: #ffffff; }
    .custom-modal-body .form-label { display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #1f2937; }
    .custom-modal-body .form-control, .custom-modal-body .form-select { width: 100%; padding: 0.75rem 0.9rem; border: 2px solid #d1d5db; background: #ffffff; color: #000000; font-size: 0.9rem; border-radius: 10px; transition: all 0.2s ease; }
    .custom-modal-body .form-control:focus, .custom-modal-body .form-select:focus { outline: none; border-color: #1a1a1a; box-shadow: 0 0 0 4px rgba(26,26,26,0.1); }
    .custom-modal-footer { padding: 1.25rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; gap: 0.75rem; border-radius: 0 0 13px 13px; }
    
    @media (max-width: 1024px) { .stats-container { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-container { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
@php
    $totalPatients = $total ?? 0;
    $todayPatients = \App\Models\Patient::whereDate('created_at', today())->count();
    $maleCount = \App\Models\Patient::where('gender', 'male')->count();
    $femaleCount = \App\Models\Patient::where('gender', 'female')->count();
@endphp

<div class="stats-container">
    <div class="stat-box">
        <div class="stat-icon total"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="value">{{ $totalPatients }}</div>
            <div class="label">Total Patients</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon today"><i class="bi bi-person-plus-fill"></i></div>
        <div class="stat-info">
            <div class="value">{{ $todayPatients }}</div>
            <div class="label">Registered Today</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon male"><i class="bi bi-gender-male"></i></div>
        <div class="stat-info">
            <div class="value">{{ $maleCount }}</div>
            <div class="label">Male Patients</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon female"><i class="bi bi-gender-female"></i></div>
        <div class="stat-info">
            <div class="value">{{ $femaleCount }}</div>
            <div class="label">Female Patients</div>
        </div>
    </div>
</div>

<div class="patients-header">
    <div>
        <h1 style="font-size: 1.1875rem; font-weight: 700; margin: 0;"><i class="bi bi-people"></i> Patients</h1>
        <p style="color: var(--color-gray-500); font-size: 0.75rem; margin: 0;">Manage patient records</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <div class="search-box">
            <form method="GET" action="{{ route('reception.patients') }}" style="display: flex; width: 100%; gap: 0.5rem;">
                <input type="text" name="search" placeholder="Search by name or phone..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-dark" style="padding: 0.5rem 0.75rem; font-size: 0.75rem; border: 2px solid #1a1a1a; font-weight: 700;"><i class="bi bi-search"></i> Search</button>
            </form>
        </div>
        <button onclick="showAddPatientModal()" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.875rem; white-space: nowrap; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: 2px solid #059669; font-weight: 700; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
            <i class="bi bi-person-plus-fill"></i> Add New Patient
        </button>
    </div>
</div>

@if($patients->count() > 0)
    <table class="patients-table">
        <thead>
            <tr>
                <th style="width: 25%;">Name</th>
                <th style="width: 18%;">Phone</th>
                <th style="width: 10%;"><i class="bi bi-calendar-event"></i> Age / DOB</th>
                <th style="width: 10%;">Gender</th>
                <th style="width: 12%;">Last Visit</th>
                <th style="width: 25%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td><strong>{{ $patient->name }}</strong></td>
                <td>{{ $patient->phone ?? '-' }}</td>
                <td>
                    @if($patient->birthdate)
                        <span style="font-weight: 600;">{{ $patient->calculated_age }}</span>
                        <small style="display: block; color: #6b7280; font-size: 0.7rem;">
                            <i class="bi bi-calendar-event"></i> {{ $patient->birthdate->format('M d, Y') }}
                        </small>
                    @else
                        {{ $patient->age ?? '-' }}
                    @endif
                </td>
                <td>{{ $patient->gender ? ucfirst($patient->gender) : '-' }}</td>
                <td>
                    @php
                        $lastVisit = $patient->visits()->latest('created_at')->first();
                    @endphp
                    {{ $lastVisit ? $lastVisit->created_at->format('M d, Y') : '-' }}
                </td>
                <td style="display: flex; gap: 0.3rem;">
                    <button class="action-btn primary" onclick="editPatient({{ $patient->id }}, '{{ $patient->name }}', '{{ $patient->phone }}', '{{ $patient->birthdate ? $patient->birthdate->format('Y-m-d') : '' }}', {{ $patient->age }}, '{{ $patient->gender }}', '{{ $patient->address }}')"><i class="bi bi-pencil"></i> Edit</button>
                    <button class="action-btn danger" onclick="deletePatient({{ $patient->id }}, '{{ $patient->name }}')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($patients->hasPages())
    <div class="pagination">
        @if($patients->onFirstPage())
            <span style="opacity: 0.5;">← Previous</span>
        @else
            <a href="{{ $patients->previousPageUrl() }}">← Previous</a>
        @endif

        @for($i = 1; $i <= $patients->lastPage(); $i++)
            @if($i == $patients->currentPage())
                <span class="active">{{ $i }}</span>
            @else
                <a href="{{ $patients->url($i) }}">{{ $i }}</a>
            @endif
        @endfor

        @if($patients->hasMorePages())
            <a href="{{ $patients->nextPageUrl() }}">Next →</a>
        @else
            <span style="opacity: 0.5;">Next →</span>
        @endif
    </div>
    @endif
@else
    <div class="empty-state">
        <i class="bi bi-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
        <h5>No patients found</h5>
        @if(request('search'))
            <p>Try a different search term</p>
        @else
            <p>Patients will appear here when registered</p>
        @endif
    </div>
@endif

<!-- Includes test scripts for Add New Patient Modal -->
<div class="custom-modal" id="add-modal">
    <div class="custom-modal-dialog" onclick="event.stopPropagation()">
        <div class="custom-modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h5><i class="bi bi-person-plus-fill"></i> Add New Patient</h5>
            <button type="button" class="btn-close" onclick="closeAddModal()"></button>
        </div>
        <div class="custom-modal-body">
            <form action="{{ route('reception.enqueue') }}" method="POST" id="add-patient-form">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label"><i class="bi bi-person-circle"></i> Patient Name *</label>
                    <input type="text" name="name" class="form-control" required minlength="2" placeholder="Enter full name">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-telephone"></i> Phone Number *</label>
                        <input type="tel" name="phone" id="add-phone" class="form-control" placeholder="Enter phone number" required oninput="handlePhoneInput(this, 'add')">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Accepts: 8866445015, +91...</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="patient@example.com">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Optional</small>
                    </div>
                </div>
                
                <div id="add-phone-suggestions" style="display: none; margin-bottom: 1rem; padding: 0.75rem; background: #fffbeb; border: 2px solid #fbbf24; border-radius: 8px;">
                    <div style="color: #92400e; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="bi bi-people-fill"></i> Related Patients Found:
                    </div>
                    <div id="add-phone-suggestions-list"></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-calendar-event"></i> Date of Birth</label>
                        <input type="date" id="add-birthdate" name="birthdate" class="form-control" onchange="calculateAgeFromBirthdate()" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-gender-ambiguous"></i> Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3" id="add-age-display" style="display: none;">
                    <label class="form-label"><i class="bi bi-person-badge"></i> Age (Auto-calculated)</label>
                    <input type="number" id="add-age" name="age" class="form-control" readonly style="background: #f3f4f6; cursor: not-allowed;">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Enter address">
                </div>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" onclick="closeAddModal()" class="btn btn-outline-secondary" style="font-weight: 600;">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button type="button" onclick="submitPatientForm('register')" class="btn btn-primary" style="font-weight: 700; box-shadow: 0 4px 12px rgba(13,110,253,0.3);">
                <i class="bi bi-person-check"></i> Register Only
            </button>
            <button type="button" onclick="submitPatientForm('queue')" class="btn btn-success" style="font-weight: 700; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                <i class="bi bi-plus-circle"></i> Register & Queue
            </button>
        </div>
    </div>
</div>

<!-- Includes test scripts for Edit Patient Modal -->
<div class="custom-modal" id="edit-modal">
    <div class="custom-modal-dialog" onclick="event.stopPropagation()">
        <div class="custom-modal-header" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
            <h5 id="modal-title"><i class="bi bi-pencil-square"></i> Edit Patient Info</h5>
            <button type="button" class="btn-close" onclick="closeModal()"></button>
        </div>
        <div class="custom-modal-body">
            <form id="edit-form" onsubmit="submitEditForm(event)">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-circle"></i> Patient Name *</label>
                    <input type="text" id="edit-name" name="name" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-telephone"></i> Phone</label>
                        <input type="tel" id="edit-phone" name="phone" class="form-control" placeholder="Enter phone number" oninput="handlePhoneInput(this, 'edit')">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Accepts any format</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                        <input type="email" id="edit-email" name="email" class="form-control" placeholder="patient@example.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: #0891b2;"><i class="bi bi-calendar-event"></i> Date of Birth *</label>
                        <input type="date" id="edit-birthdate" name="birthdate" class="form-control" onchange="calculateAndDisplayAge()" style="border: 2px solid #0891b2;">
                        <small class="d-block mt-1" style="color: #0891b2; font-size: 0.75rem;"><i class="bi bi-info-circle-fill"></i> Auto-calculates age</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-gender-ambiguous"></i> Gender</label>
                        <select id="edit-gender" name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div id="age-display" style="margin-bottom: 1rem; padding: 0.75rem; background: #e0f2fe; border-radius: 8px; border: 1px solid #bae6fd; display: none;">
                    <strong style="color: #0c4a6e; font-size: 0.9rem;">
                        <i class="bi bi-person-badge"></i> Calculated Age: <span id="age-value" style="font-size: 1.1rem; color: #0891b2; font-weight: 700;">-</span> years
                    </strong>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> Address</label>
                    <input type="text" id="edit-address" name="address" class="form-control">
                </div>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-outline-secondary" onclick="closeModal()" style="font-weight: 600;">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button type="submit" form="edit-form" class="btn btn-primary" style="font-weight: 700; box-shadow: 0 4px 12px rgba(13,110,253,0.3);">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPatientId = null;

/**
 * Clean phone number to store consistently (10 digits only)
 */
function cleanPhoneNumber(phone) {
    if (!phone) return '';
    
    // Remove all non-numeric characters
    let cleaned = phone.replace(/[^0-9]/g, '');
    
    // Remove country code if present
    if (cleaned.startsWith('91') && cleaned.length > 10) {
        cleaned = cleaned.substring(2);
    }
    
    // Remove leading 0 if present
    if (cleaned.startsWith('0')) {
        cleaned = cleaned.substring(1);
    }
    
    // Take only first 10 digits
    return cleaned.substring(0, 10);
}

/**
 * Handle phone input - accepts any format, searches patients, stores clean
 */
function handlePhoneInput(input, context) {
    const rawValue = input.value;
    
    // Clean the phone number for searching
    const cleanedPhone = cleanPhoneNumber(rawValue);
    
    // Search for related patients using cleaned phone
    if (cleanedPhone.length >= 10) {
        searchPatientsByPhone(cleanedPhone, context);
    } else {
        const suggestionsDiv = document.getElementById(`${context}-phone-suggestions`);
        if (suggestionsDiv) {
            suggestionsDiv.style.display = 'none';
        }
    }
    
    // Store cleaned phone in hidden field or update before submit
    input.dataset.cleanedPhone = cleanedPhone;
}

/**
 * Calculate age from birthdate and display it
 */
function calculateAndDisplayAge() {
    const birthdateInput = document.getElementById('edit-birthdate');
    const ageDisplay = document.getElementById('age-display');
    const ageValue = document.getElementById('age-value');
    
    if (birthdateInput.value) {
        const birthdate = new Date(birthdateInput.value);
        const today = new Date();
        
        let age = today.getFullYear() - birthdate.getFullYear();
        const monthDiff = today.getMonth() - birthdate.getMonth();
        
        // Adjust age if birthday hasn't occurred yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        
        if (age >= 0 && age <= 150) {
            ageValue.textContent = age;
            ageDisplay.style.display = 'block';
        } else {
            ageDisplay.style.display = 'none';
        }
    } else {
        ageDisplay.style.display = 'none';
    }
}

function editPatient(id, name, phone, birthdate, age, gender, address) {
    currentPatientId = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-phone').value = phone || '';
    document.getElementById('edit-birthdate').value = birthdate || '';
    document.getElementById('edit-gender').value = gender || '';
    document.getElementById('edit-address').value = address || '';
    
    // Calculate and display age if birthdate is provided
    if (birthdate) {
        calculateAndDisplayAge();
    } else {
        document.getElementById('age-display').style.display = 'none';
    }
    
    document.getElementById('edit-modal').classList.add('show');
}

function closeModal() {
    document.getElementById('edit-modal').classList.remove('show');
    currentPatientId = null;
}

// Close modal when clicking outside the dialog
document.addEventListener('DOMContentLoaded', function() {
    const backdrop = document.getElementById('edit-modal');
    backdrop.addEventListener('click', function(e) {
        if (e.target === backdrop) {
            closeModal();
        }
    });
});

async function submitEditForm(event) {
    event.preventDefault();
    
    // Validate birthdate is provided
    const birthdate = document.getElementById('edit-birthdate').value;
    if (!birthdate) {
        window.showGlobalNotification('Please select a date of birth', 'error');
        return;
    }
    
    const formData = new FormData(document.getElementById('edit-form'));
    const data = Object.fromEntries(formData);
    
    // Remove age from data as it will be calculated from birthdate
    delete data.age;
    
    try {
        const response = await axios.put(`/api/patients/${currentPatientId}`, data);
        if (response.data.success) {
            window.showGlobalNotification('Patient updated successfully!', 'success');
            closeModal();
            location.reload();
        }
    } catch (error) {
        const errorMsg = error.response?.data?.message || 'Please try again';
        window.showGlobalNotification('Error: ' + errorMsg, 'error');
    }
}

/**
 * Submit patient form with action (register only or register & queue)
 */
async function submitPatientForm(action) {
    const form = document.getElementById('add-patient-form');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Clean phone number
    const phoneInput = document.getElementById('add-phone');
    if (phoneInput && phoneInput.value) {
        const cleanedPhone = cleanPhoneNumber(phoneInput.value);
        if (cleanedPhone.length < 10) {
            alert('Please enter a valid phone number with at least 10 digits');
            return;
        }
        formData.set('phone', cleanedPhone);
    }
    
    // Add action type
    formData.append('action', action);
    
    // Disable buttons during submission
    const buttons = document.querySelectorAll('#add-patient-form button[type="button"]');
    const originalTexts = Array.from(buttons).map(btn => btn.innerHTML);
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    });
    
    try {
        const response = await axios.post(form.action, formData, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        if (response.data.success) {
            if (action === 'queue') {
                window.showGlobalNotification('Patient registered and added to queue successfully!', 'success');
            } else {
                window.showGlobalNotification('Patient registered successfully!', 'success');
            }
            closeAddModal();
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        // Re-enable buttons
        buttons.forEach((btn, index) => {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalTexts[index];
        });
        
        const errorMsg = error.response?.data?.message || error.response?.data?.error || error.message || 'Please try again';
        window.showGlobalNotification('Error: ' + errorMsg, 'error');
    }
}

async function deletePatient(id, name) {
    if (!confirm(`Delete patient "${name}"? This action cannot be undone.`)) return;
    
    try {
        const response = await axios.delete(`/api/patients/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        if (response.data.success) {
            window.showGlobalNotification('Patient deleted successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        const errorMsg = error.response?.data?.message || error.response?.data?.error || error.message || 'Please try again';
        window.showGlobalNotification('Error: ' + errorMsg, 'error');
    }
}

// Modal is closed only via closeModal() function called by buttons
// This prevents accidental closing when interacting with form fields
// Users can click X button or Cancel button to close the modal

// Add Patient Modal Functions
function showAddPatientModal() {
    document.getElementById('add-modal').classList.add('show');
}

function closeAddModal() {
    document.getElementById('add-modal').classList.remove('show');
}

// Calculate age from birthdate for Add Patient modal
function calculateAgeFromBirthdate() {
    const birthdate = document.getElementById('add-birthdate').value;
    if (birthdate) {
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        document.getElementById('add-age').value = age;
        document.getElementById('add-age-display').style.display = 'block';
    } else {
        document.getElementById('add-age-display').style.display = 'none';
    }
}

// Close add modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const addBackdrop = document.getElementById('add-modal');
    if (addBackdrop) {
        addBackdrop.addEventListener('click', function(e) {
            if (e.target === addBackdrop) {
                closeAddModal();
            }
        });
    }
});

// Clean phone numbers before form submission
document.addEventListener('DOMContentLoaded', function() {
    // Handle add patient form
    const addForm = document.getElementById('add-patient-form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('add-phone');
            if (phoneInput && phoneInput.value) {
                const cleanedPhone = cleanPhoneNumber(phoneInput.value);
                if (cleanedPhone.length < 10) {
                    e.preventDefault();
                    alert('Please enter a valid phone number with at least 10 digits');
                    return false;
                }
                phoneInput.value = cleanedPhone;
            }
        });
    }
    
    // Handle edit patient form
    const editForm = document.getElementById('edit-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('edit-phone');
            if (phoneInput && phoneInput.value) {
                const cleanedPhone = cleanPhoneNumber(phoneInput.value);
                if (cleanedPhone.length > 0 && cleanedPhone.length < 10) {
                    e.preventDefault();
                    alert('Please enter a valid phone number with at least 10 digits');
                    return false;
                }
                phoneInput.value = cleanedPhone;
            }
        });
    }
});

// Prevent form submission when pressing Enter on input fields (except textarea)
document.addEventListener('keypress', function(e) {
    const form = document.getElementById('edit-form');
    if (form && form.contains(e.target) && e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
    }
});

// Phone search functionality for family linking
let phoneSearchTimeout;
async function searchPatientsByPhone(phone, context) {
    clearTimeout(phoneSearchTimeout);
    
    const suggestionsDiv = document.getElementById(`${context}-phone-suggestions`);
    const suggestionsList = document.getElementById(`${context}-phone-suggestions-list`);
    
    // Only search when phone has at least 10 digits
    if (!phone || phone.length < 10) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    phoneSearchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get('/reception/search-by-phone', {
                params: { phone: phone }
            });
            
            if (response.data.success && response.data.patients.length > 0) {
                // Show suggestions
                suggestionsList.innerHTML = response.data.patients.map(patient => `
                    <div style="padding: 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        <div style="font-weight: 600; color: #1f2937;">👤 ${patient.name}</div>
                        <div style="color: #6b7280; font-size: 0.75rem;">
                            📞 ${patient.phone} ${patient.age ? `• Age: ${patient.age}` : ''} ${patient.gender ? `• ${patient.gender}` : ''}
                        </div>
                        <div style="color: #059669; font-size: 0.75rem; margin-top: 0.25rem;">
                            <i class="bi bi-info-circle"></i> You can add family member with same phone
                        </div>
                    </div>
                `).join('');
                suggestionsDiv.style.display = 'block';
            } else {
                suggestionsDiv.style.display = 'none';
            }
        } catch (error) {
            console.error('Error searching patients:', error);
            suggestionsDiv.style.display = 'none';
        }
    }, 500);
}
</script>
@endsection
