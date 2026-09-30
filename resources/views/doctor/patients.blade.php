@extends('layouts.app')

@section('title', 'Patient History - Doctor - ClinicFlow')

@section('styles')
<style>
    /* Visit History */
    .visit-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .visit-item {
        padding: 1rem;
        border: 1px solid var(--gray-200);
        margin-bottom: 0.75rem;
        border-radius: 10px;
        background: var(--white);
        transition: all 0.2s ease;
    }

    .visit-item:hover {
        border-color: var(--gray-300);
        box-shadow: var(--shadow-sm);
    }

    .visit-item:last-child {
        margin-bottom: 0;
    }

    .visit-date {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
    }

    .visit-details {
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: var(--gray-600);
        line-height: 1.6;
    }

    .visit-details strong {
        color: var(--gray-900);
    }

    .prescription-items {
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: var(--radius-md);
        font-size: 0.75rem;
        border: 1px solid var(--gray-200);
    }

    .prescription-items ul {
        margin: 0.5rem 0 0;
        padding-left: 1.25rem;
    }

    .prescription-items li {
        margin-bottom: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title-section">
        <h1><i class="bi bi-person-lines-fill"></i> Patient History</h1>
        <p class="page-subtitle">Search patients and view their visit history</p>
    </div>
</div>

<!-- Search Form -->
<form action="{{ route('doctor.patients') }}" method="GET" class="mb-4" style="max-width: 600px;">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search by patient name or phone..."
            value="{{ $search }}" autofocus>
        <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i> Search</button>
        @if($search)
        <a href="{{ route('doctor.patients') }}" class="btn btn-light border"><i class="bi bi-x"></i> Clear</a>
        @endif
    </div>
</form>

@if($search)
@if($patients->count() > 0)
<p class="text-muted mb-3 fs-6">
    Found <strong>{{ $patients->count() }}</strong> patient(s) matching "<strong>{{ $search }}</strong>"
</p>

@foreach($patients as $patient)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <div>
            <h5 class="mb-1 text-dark fw-bold">
                <i class="bi bi-person-circle text-muted me-2"></i>
                {{ $patient->name }}
            </h5>
            <div class="d-flex flex-wrap gap-3 text-muted fs-7 mt-2">
                <span><i class="bi bi-telephone"></i> {{ $patient->phone ?? 'No phone' }}</span>
                @if($patient->birthdate)
                <span><i class="bi bi-person-badge"></i> {{ $patient->calculated_age }} years <small>(DOB: {{
                        $patient->birthdate->format('M d, Y') }})</small></span>
                @elseif($patient->age)
                <span><i class="bi bi-person"></i> {{ $patient->age }} years</span>
                @endif
                @if($patient->gender)
                <span><i class="bi bi-gender-ambiguous"></i> {{ ucfirst($patient->gender) }}</span>
                @endif
            </div>
        </div>
        <div>
            <span class="badge bg-dark fw-semibold px-3 py-2 fs-7">
                <i class="bi bi-calendar-check me-1"></i> {{ $patient->visits->count() }} visit(s)
            </span>
        </div>
    </div>
    <div class="card-body">
        @if($patient->visits->count() > 0)
        <h6 class="text-muted fw-bold mb-3 fs-7 text-uppercase">Visit History</h6>
        <ul class="visit-list">
            @foreach($patient->visits->take(10) as $visit)
            <li class="visit-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="visit-date">
                            <i class="bi bi-calendar3"></i> {{ $visit->created_at->format('d M Y, h:i A') }}
                        </span>
                        @php
                        $statusClass = match($visit->status) {
                        'completed', 'paid' => 'badge-success',
                        'waiting' => 'badge-warning',
                        'in_consult' => 'bg-primary text-white',
                        'cancelled' => 'badge-danger',
                        'pending' => 'bg-danger text-white',
                        default => 'bg-secondary text-white'
                        };
                        @endphp
                        <span class="badge {{ $statusClass }} ms-2 px-2 py-1 text-uppercase"
                            style="font-size: 0.7rem; letter-spacing: 0.3px;">{{ str_replace('_', ' ', $visit->status)
                            }}</span>
                    </div>
                    @if($visit->bill)
                    <a href="{{ route('bills.view', $visit->bill->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-receipt"></i> View Bill
                    </a>
                    @else
                    <a href="{{ route('doctor.visit', $visit->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View Visit
                    </a>
                    @endif
                </div>

                <div class="visit-details">
                    @if($visit->diagnosis)
                    <div><strong>Diagnosis:</strong> {{ Str::limit($visit->diagnosis, 100) }}</div>
                    @endif
                    @if($visit->notes)
                    <div><strong>Notes:</strong> {{ Str::limit($visit->notes, 100) }}</div>
                    @endif

                    @if($visit->prescription && $visit->prescription->items->count() > 0)
                    <div class="prescription-items">
                        <strong>Prescribed Medicines:</strong>
                        <ul>
                            @foreach($visit->prescription->items as $item)
                            <li>{{ $item->medicine->name ?? 'Unknown' }} - {{ $item->dosage ?? '' }} (Qty: {{
                                $item->quantity }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($visit->bill)
                    <div class="mt-2">
                        <strong>Bill:</strong> ₹{{ number_format($visit->bill->total_amount, 2) }}
                        <span class="badge {{ $visit->bill->status === 'paid' ? 'badge-success' : 'badge-danger' }}">{{
                            $visit->bill->status }}</span>
                    </div>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @if($patient->visits->count() > 10)
        <p class="text-center mt-3 text-muted fs-7">
            Showing latest 10 of {{ $patient->visits->count() }} visits
        </p>
        @endif
        @else
        <div class="bg-light p-4 rounded text-center text-muted fst-italic fs-7">
            <i class="bi bi-info-circle"></i> No visit history found for this patient.
        </div>
        @endif
    </div>
</div>
@endforeach
@else
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="bi bi-search"></i>
    </div>
    <h5>No patients found</h5>
    <p>No patients matching "<strong>{{ $search }}</strong>" were found.</p>
</div>
@endif
@else
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="bi bi-person-lines-fill"></i>
    </div>
    <h5>Search for a patient</h5>
    <p>Enter a patient's name or phone number to view their visit history.</p>
</div>
@endif
@endsection