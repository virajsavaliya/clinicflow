@extends('layouts.app')

@section('title', 'Patient Visit - ' . $visit->patient->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Back to Queue
        </a>
        <h2>
            <i class="bi bi-person-vcard"></i> #{{ $visit->queue_number }} - {{ $visit->patient->name }}
        </h2>
    </div>
</div>

<div class="row">
    <!-- Patient Details -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Patient Information</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $visit->patient->name }}</p>
                <p class="mb-2"><strong>Phone:</strong> {{ $visit->patient->phone ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Age:</strong> {{ $visit->patient->age ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Gender:</strong> {{ $visit->patient->gender ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Visit Status:</strong> <span class="badge bg-info">{{ ucfirst($visit->status) }}</span></p>
                <p class="mb-0"><strong>Start Time:</strong> {{ $visit->started_at?->format('H:i') ?? 'Not started' }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-history"></i> Medical History</h6>
            </div>
            <div class="card-body" id="patient-history" style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;">
                <div class="text-center text-muted"><small>Loading history...</small></div>
            </div>
        </div>

        @if($visit->prescription)
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Existing Prescription</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">Created: {{ $visit->prescription->created_at->format('Y-m-d H:i') }}</small>
                <p class="mb-0 mt-2">
                    <strong>Items:</strong> {{ $visit->prescription->items->count() }}
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Prescription Form -->
    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-stethoscope"></i> Diagnosis & Prescription</h6>
            </div>
            <div class="card-body">
                <form id="prescription-form">
                    @csrf

                    <!-- Diagnosis Notes -->
                    <div class="mb-4">
                        <label for="diagnosis_notes" class="form-label">Diagnosis Notes</label>
                        <textarea class="form-control" id="diagnosis_notes" name="diagnosis_notes" rows="4" placeholder="Enter diagnosis and observations...">{{ $visit->notes ?? '' }}</textarea>
                    </div>

                    <!-- Medicines Section -->
                    <h6 class="mb-3 border-bottom pb-2"><i class="bi bi-capsule"></i> Prescribed Medicines</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Search & Add Medicine</label>
                        <div class="position-relative">
                            <div class="input-group">
                                <input type="text" class="form-control" id="medicine-search" placeholder="Search by name or barcode..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="add-medicine-btn">Add</button>
                            </div>
                            <div id="medicine-suggestions" class="list-group mt-1" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: white; border: 1px solid #dee2e6;"></div>
                        </div>
                        <small class="text-muted d-block mt-2">💡 Tip: Type at least 2 characters to search medicines</small>
                    </div>

                    <!-- Medicines List -->
                    <div id="medicines-list" class="mb-4">
                        @if($visit->prescription && $visit->prescription->items->count())
                            @foreach($visit->prescription->items as $item)
                                <div class="medicine-item card mb-2">
                                    <div class="card-body row align-items-center">
                                        <div class="col-md-5">
                                            <h6 class="mb-1">{{ $item->medicine->name }}</h6>
                                            <small class="text-muted">Unit Price: ₹{{ number_format($item->unit_price, 2) }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Qty</span>
                                                <input type="number" class="form-control qty-input" min="1" value="{{ $item->qty }}" data-medicine-id="{{ $item->medicine_id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <p class="mb-0"><strong>₹{{ number_format($item->subtotal, 2) }}</strong></p>
                                            <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removeMedicine(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Doctor Charges Section -->
                    <h6 class="mb-3 border-bottom pb-2"><i class="bi bi-list-ul"></i> Doctor Charges</h6>

                    <div id="charges-list" class="mb-4">
                        @if($doctorCharges->count())
                            @foreach($doctorCharges as $charge)
                                <div class="form-check mb-2">
                                    <input class="form-check-input charge-checkbox" type="checkbox" name="selected_charges[]" value="{{ $charge->id }}" id="charge_{{ $charge->id }}" data-price="{{ $charge->price }}">
                                    <label class="form-check-label" for="charge_{{ $charge->id }}">
                                        {{ $charge->title }} - ₹{{ number_format($charge->price, 2) }}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted"><small>No charges configured</small></p>
                        @endif
                    </div>

                    <!-- Optional Taxes -->
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <label for="taxes" class="form-label">Taxes (Optional)</label>
                            <input type="number" step="0.01" class="form-control" id="taxes" name="taxes" min="0" value="0">
                        </div>
                    </div>

                    <!-- Bill Summary -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Medicine Total:</strong> <span id="medicine-total">₹0.00</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Doctor Charges:</strong> <span id="charges-total">₹0.00</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Taxes:</strong> <span id="taxes-display">₹0.00</span>
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h5 class="mb-0">
                                        <strong>Grand Total:</strong> <span id="grand-total" style="color: #27ae60; font-size: 24px;">₹0.00</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success flex-grow-1" id="finalize-btn">
                            <i class="bi bi-check-circle"></i> Finalize Prescription & Bill
                        </button>
                        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const visitId = {{ $visit->id }};
const doctorProfile = {!! json_encode($doctorProfile) !!};
let selectedMedicines = [];
let selectedCharges = [];

// Medicine search with autocomplete
const medicineSearchInput = document.getElementById('medicine-search');
const medicineSuggestionsDiv = document.getElementById('medicine-suggestions');

medicineSearchInput.addEventListener('input', async (e) => {
    const query = e.target.value.trim();
    
    // Show "None" option when input is empty or very short
    if (query.length === 0) {
        medicineSuggestionsDiv.innerHTML = `
            <a href="#" class="list-group-item list-group-item-action py-2" onclick="clearMedicineSearch(event)">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle me-2" style="color: #6c757d; font-size: 18px;"></i>
                    <div>
                        <strong style="color: #6c757d;">None</strong>
                        <small class="d-block text-muted">Clear selection</small>
                    </div>
                </div>
            </a>
        `;
        medicineSuggestionsDiv.style.display = 'block';
        return;
    }
    
    if (query.length < 2) {
        medicineSuggestionsDiv.style.display = 'none';
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.get('/api/medicines/search', {
            params: { q: query, limit: 8 },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        if (response.data.medicines && response.data.medicines.length > 0) {
            const html = response.data.medicines.map(m => `
                <a href="#" class="list-group-item list-group-item-action py-2 border-bottom" onclick="selectMedicine(event, ${m.id}, '${escapeHtml(m.name)}', ${m.unit_price}, ${m.current_stock})">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex-grow: 1;">
                            <strong style="color: #212529;">${escapeHtml(m.name)}</strong>
                            ${m.generic_name ? `<small class="d-block text-muted">${escapeHtml(m.generic_name)}</small>` : ''}
                            <small class="d-block text-muted">₹${parseFloat(m.unit_price).toFixed(2)}</small>
                        </div>
                        <div style="text-align: right; margin-left: 10px;">
                            <span style="font-weight: 600; color: ${m.current_stock > 0 ? '#16a34a' : '#dc2626'};">
                                Stock: ${m.current_stock}
                            </span>
                        </div>
                    </div>
                </a>
            `).join('');
            
            medicineSuggestionsDiv.innerHTML = html;
            medicineSuggestionsDiv.style.display = 'block';
        } else {
            medicineSuggestionsDiv.innerHTML = `
                <div class="list-group-item py-3 text-center" style="color: #6c757d;">
                    <i class="bi bi-search" style="font-size: 24px; opacity: 0.5;"></i>
                    <div><small>No medicines found matching "${escapeHtml(query)}"</small></div>
                </div>
            `;
            medicineSuggestionsDiv.style.display = 'block';
        }
    } catch (error) {
        console.error('Search error:', error);
        medicineSuggestionsDiv.innerHTML = `
            <div class="list-group-item py-3 text-center text-danger">
                <small>Error searching medicines</small>
            </div>
        `;
        medicineSuggestionsDiv.style.display = 'block';
    }
});

// Helper function to escape HTML special characters
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Clear medicine search
function clearMedicineSearch(e) {
    e.preventDefault();
    medicineSearchInput.value = '';
    medicineSuggestionsDiv.style.display = 'none';
}

// Close suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!medicineSearchInput.contains(e.target) && !medicineSuggestionsDiv.contains(e.target)) {
        medicineSuggestionsDiv.style.display = 'none';
    }
});

// Select medicine from search results
function selectMedicine(e, medicineId, medicineName, unitPrice, stock) {
    e.preventDefault();
    
    // Check if medicine already added
    const existing = document.querySelector(`[data-medicine-id="${medicineId}"]`);
    if (existing) {
        alert('⚠️ This medicine is already added to the prescription!');
        medicineSearchInput.value = '';
        medicineSuggestionsDiv.style.display = 'none';
        return;
    }

    if (stock <= 0) {
        alert('⚠️ This medicine is currently out of stock!');
        return;
    }

    const medicineDiv = document.createElement('div');
    medicineDiv.className = 'medicine-item card mb-2';
    medicineDiv.innerHTML = `
        <div class="card-body row align-items-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);">
            <div class="col-md-5">
                <h6 class="mb-1" style="color: #212529;">${escapeHtml(medicineName)}</h6>
                <small class="text-muted">Unit Price: <strong>₹${parseFloat(unitPrice).toFixed(2)}</strong> | Available Stock: <span style="color: #16a34a; font-weight: 600;">${stock} units</span></small>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Quantity</span>
                    <input type="number" class="form-control qty-input" min="1" max="${stock}" value="1" data-medicine-id="${medicineId}" data-price="${unitPrice}" style="font-weight: 600;">
                </div>
            </div>
            <div class="col-md-3 text-end">
                <p class="mb-0 medicine-subtotal"><strong style="font-size: 1.1rem; color: #27ae60;">₹${parseFloat(unitPrice).toFixed(2)}</strong></p>
                <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removeMedicine(this)" title="Remove this medicine">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>
    `;

    document.getElementById('medicines-list').appendChild(medicineDiv);
    medicineSearchInput.value = '';
    medicineSuggestionsDiv.style.display = 'none';

    // Add quantity change listener
    medicineDiv.querySelector('.qty-input').addEventListener('change', calculateTotals);
    medicineDiv.querySelector('.qty-input').addEventListener('keyup', calculateTotals);

    calculateTotals();
    medicineSearchInput.focus();
}

// Remove medicine from list
function removeMedicine(btn) {
    btn.closest('.medicine-item').remove();
    calculateTotals();
}

// Calculate totals
function calculateTotals() {
    let medicineTotal = 0;
    
    document.querySelectorAll('.medicine-item').forEach(item => {
        const qty = parseInt(item.querySelector('.qty-input').value);
        const price = parseFloat(item.querySelector('.qty-input').dataset.price);
        const subtotal = qty * price;
        item.querySelector('.medicine-subtotal').innerHTML = `<strong style="font-size: 1.1rem; color: #27ae60;">₹${subtotal.toFixed(2)}</strong>`;
        medicineTotal += subtotal;
    });

    let chargesTotal = 0;
    document.querySelectorAll('.charge-checkbox:checked').forEach(checkbox => {
        chargesTotal += parseFloat(checkbox.dataset.price);
    });

    const taxes = parseFloat(document.getElementById('taxes').value) || 0;
    const grandTotal = medicineTotal + chargesTotal + taxes;

    document.getElementById('medicine-total').textContent = `₹${medicineTotal.toFixed(2)}`;
    document.getElementById('charges-total').textContent = `₹${chargesTotal.toFixed(2)}`;
    document.getElementById('taxes-display').textContent = `₹${taxes.toFixed(2)}`;
    document.getElementById('grand-total').textContent = `₹${grandTotal.toFixed(2)}`;
}

// Charge checkbox listeners
document.querySelectorAll('.charge-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', calculateTotals);
});

// Taxes input listener
document.getElementById('taxes').addEventListener('change', calculateTotals);

// Finalize prescription
document.getElementById('finalize-btn').addEventListener('click', async () => {
    // Gather form data
    const medicines = [];
    document.querySelectorAll('.medicine-item').forEach(item => {
        const medicineId = parseInt(item.querySelector('.qty-input').dataset.medicineId);
        const qty = parseInt(item.querySelector('.qty-input').value);
        medicines.push({ medicine_id: medicineId, qty: qty });
    });

    const selectedCharges = [];
    document.querySelectorAll('.charge-checkbox:checked').forEach(checkbox => {
        selectedCharges.push(parseInt(checkbox.value));
    });

    // Allow prescription without medicines (e.g., consultation only)
    if (medicines.length === 0 && selectedCharges.length === 0) {
        if (!confirm('No medicines or charges selected. Create consultation-only prescription?')) {
            return;
        }
    }

    const data = {
        diagnosis_notes: document.getElementById('diagnosis_notes').value,
        items: medicines,
        selected_charges: selectedCharges,
        taxes: parseFloat(document.getElementById('taxes').value) || 0
    };

    try {
        const response = await axios.post(
            `{{ route("doctor.finalize-prescription", ":id") }}`.replace(':id', visitId),
            data,
            {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        );

        if (response.data.success) {
            alert('Prescription finalized successfully!');
            const billId = response.data.bill.id;
            window.location.href = `/bills/${billId}/view`;
        }
    } catch (error) {
        const errData = error.response?.data;
        if (errData?.errors?.medicines) {
            const medicineErrors = errData.errors.medicines.map(m => 
                `${m.name}: requested ${m.requested}, available ${m.available}`
            ).join('\n');
            alert('Insufficient stock:\n' + medicineErrors);
        } else {
            alert('Error: ' + (errData?.message || error.message));
        }
    }
});

// Initial calculation
calculateTotals();

// Load patient medical history
async function loadPatientHistory() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.get('{{ route("doctor.patient-history", ":id") }}'.replace(':id', {{ $visit->patient->id }}), {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const { history } = response.data;
        const historyDiv = document.getElementById('patient-history');
        
        if (history.length === 0) {
            historyDiv.innerHTML = '<small class="text-muted">No previous visits</small>';
            return;
        }
        
        historyDiv.innerHTML = history.map(visit => `
            <div style="margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e5e5;">
                <div style="font-weight: 600; margin-bottom: 0.25rem;">${visit.date}</div>
                <div style="margin-bottom: 0.25rem; color: #666;">
                    <strong>Dr:</strong> ${visit.doctor}
                </div>
                ${visit.diagnosis ? `
                    <div style="margin-bottom: 0.25rem; color: #666;">
                        <strong>Problem:</strong><br><small>${visit.diagnosis}</small>
                    </div>
                ` : ''}
                ${visit.medicines.length > 0 ? `
                    <div style="color: #666;">
                        <strong>Medicines:</strong><br>
                        ${visit.medicines.map(m => `<small>• ${m.name} (${m.dosage || 'as prescribed'})</small>`).join('<br>')}
                    </div>
                ` : ''}
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading history:', error);
        document.getElementById('patient-history').innerHTML = '<small class="text-danger">Error loading history</small>';
    }
}

loadPatientHistory();
</script>
@endsection
