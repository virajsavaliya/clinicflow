@extends('layouts.app')

@section('title', 'Invoice #' . $bill->id)

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="bi bi-receipt"></i> Invoice #{{ $bill->id }}</h2>
            <div>
                @if(auth()->user()->isDoctor())
                <a href="{{ route('doctor.visit', $bill->visit->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Edit Visit
                </a>
                @endif
                <a href="{{ route('bills.pdf', $bill->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-download"></i> Download PDF
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card" style="border: 1px solid #bbb;">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="row mb-4 pb-3 border-bottom">
                    <div class="col-md-6">
                        @if($doctorProfile && $doctorProfile->getClinicLogoUrl())
                            <img src="{{ $doctorProfile->getClinicLogoUrl() }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
                        @endif
                        <h4>{{ $doctorProfile?->clinic_name ?? 'Clinic' }}</h4>
                        <p class="text-muted mb-0">Dr. {{ $bill->visit->doctor->name }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>INVOICE</h5>
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $bill->id }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $bill->created_at->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge bg-{{ $bill->status === 'paid' ? 'success' : ($bill->status === 'partial' ? 'warning' : 'danger') }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Patient Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted">Bill To</h6>
                        <p class="mb-1"><strong>{{ $bill->visit->patient->name }}</strong></p>
                        <p class="mb-1">Phone: {{ $bill->visit->patient->phone ?? 'N/A' }}</p>
                        <p class="mb-1">Age: {{ $bill->visit->patient->age ?? 'N/A' }} | Gender: {{ $bill->visit->patient->gender ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="mb-1"><strong>Visit ID:</strong> {{ $bill->visit->id }}</p>
                        <p class="mb-1"><strong>Queue #:</strong> {{ $bill->visit->queue_number }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Item Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->items as $item)
                            <tr>
                                <td>
                                    {{ $item->title }}
                                    <br><small class="text-muted">({{ ucfirst($item->item_type) }})</small>
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end"><strong>₹{{ number_format($item->subtotal, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row mb-4">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Medicine Total:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($bill->medicine_total, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Doctor Charges:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($bill->doctor_charges_total, 2) }}</strong></td>
                            </tr>
                            @if($bill->taxes)
                            <tr>
                                <td><strong>Taxes:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($bill->taxes, 2) }}</strong></td>
                            </tr>
                            @endif
                            <tr class="border-top border-bottom">
                                <td class="pt-3"><h5 class="mb-0">Total Amount</h5></td>
                                <td class="text-end pt-3"><h5 class="mb-0">₹{{ number_format($bill->total_amount, 2) }}</h5></td>
                            </tr>
                            <tr>
                                <td><strong>Paid Amount:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($bill->paid_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Balance Due:</strong></td>
                                <td class="text-end">
                                    <strong style="color: {{ $bill->getBalanceDue() > 0 ? '#e74c3c' : '#27ae60' }};">
                                        ₹{{ number_format($bill->getBalanceDue(), 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                @if($bill->visit->notes)
                <div class="mb-4 p-3 bg-light border">
                    <h6 class="text-uppercase text-muted">Diagnosis Notes</h6>
                    <p class="mb-0">{{ $bill->visit->notes }}</p>
                </div>
                @endif

                <!-- Footer -->
                <div class="text-center text-muted border-top pt-3">
                    <p class="mb-0">Thank you for your visit!</p>
                    <p class="mb-0" style="font-size: 12px;">Generated on {{ now()->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-check-circle"></i> Invoice Paid</h6>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <p class="mb-2">
                    <strong>Medicine Total:</strong><br>
                    ₹{{ number_format($bill->medicine_total, 2) }}
                </p>
                <p class="mb-2">
                    <strong>Doctor Charges:</strong><br>
                    ₹{{ number_format($bill->doctor_charges_total, 2) }}
                </p>
                @if($bill->taxes)
                <p class="mb-2">
                    <strong>Taxes:</strong><br>
                    ₹{{ number_format($bill->taxes, 2) }}
                </p>
                @endif
                <hr>
                <p class="mb-2">
                    <strong>Total Amount:</strong><br>
                    <span style="font-size: 16px; color: #2c3e50;">₹{{ number_format($bill->total_amount, 2) }}</span>
                </p>
                <p class="mb-0">
                    <strong>Paid Amount:</strong><br>
                    ₹{{ number_format($bill->paid_amount, 2) }}
                </p>
                <hr>
                <div class="alert alert-success mt-3 mb-3" style="padding: 0.75rem; margin-bottom: 1rem;">
                    <i class="bi bi-check-circle"></i> <strong>Payment Complete</strong>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                    <button onclick="goToNextPatient()" class="btn btn-primary" style="width: 100%; padding: 0.5rem;">
                        <i class="bi bi-arrow-right-circle"></i> Next Patient
                    </button>
                    <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary" style="width: 100%; text-decoration: none; padding: 0.5rem;">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const billId = {{ $bill->id }};
    const patientName = '{{ $bill->visit->patient->name }}';
    const visitId = {{ $bill->visit->id }};

    async function goToNextPatient() {
        try {
            // Send notification to reception about next patient
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Create notification for next patient
            const notificationResponse = await axios.post('/api/notifications', {
                type: 'patient_called',
                title: '👉 Next Patient Called',
                message: `${patientName} (Visit ID: ${visitId}) is ready for checkout.`,
                reference_type: 'visit',
                reference_id: visitId
            }, {
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });

            // Show success message to doctor
            if (typeof window.showGlobalNotification === 'function') {
                window.showGlobalNotification(`Notification sent to reception for ${patientName}!`, 'success');
            }

            // Redirect to dashboard after brief delay
            setTimeout(() => {
                window.location.href = '{{ route("doctor.dashboard") }}';
            }, 800);
        } catch (error) {
            console.error('Error sending notification:', error);
            // Still redirect even if notification fails
            window.location.href = '{{ route("doctor.dashboard") }}';
        }
    }
</script>
@endsection
