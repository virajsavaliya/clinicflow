<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription - {{ $patient->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .prescription-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .clinic-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .clinic-name {
            font-size: 22px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .clinic-address {
            font-size: 10px;
            color: #666;
        }
        .doctor-info {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        .doctor-details {
            display: table-cell;
            vertical-align: top;
        }
        .doctor-name {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }
        .doctor-quals {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        .doctor-reg {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }
        .rx-info {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }
        .rx-date {
            font-size: 10px;
            color: #666;
        }
        .rx-number {
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Patient Info */
        .patient-section {
            background: #f7fafc;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .patient-row {
            display: table;
            width: 100%;
        }
        .patient-cell {
            display: table-cell;
            width: 33%;
            padding: 3px 0;
        }
        .patient-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
        }
        .patient-value {
            font-size: 12px;
            font-weight: 600;
            color: #2d3748;
        }
        
        /* RX Symbol */
        .rx-symbol {
            font-size: 36px;
            font-weight: bold;
            color: #2b6cb0;
            font-family: serif;
            margin: 15px 0 10px;
        }
        
        /* Medicines Table */
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .medicines-table th {
            background: #2d3748;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .medicines-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .medicines-table tr:nth-child(even) {
            background: #f7fafc;
        }
        .medicine-name {
            font-weight: 600;
            color: #2d3748;
        }
        .medicine-generic {
            font-size: 9px;
            color: #888;
            font-style: italic;
        }
        .dosage-instructions {
            color: #e53e3e;
            font-weight: 600;
        }
        
        /* Diagnosis & Notes */
        .section-box {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #4a5568;
            text-transform: uppercase;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-content {
            font-size: 11px;
            color: #2d3748;
            padding: 5px 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .signature-section {
            display: table;
            width: 100%;
        }
        .follow-up {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }
        .follow-up-text {
            font-size: 10px;
            color: #666;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: right;
        }
        .signature-line {
            width: 150px;
            border-top: 1px solid #333;
            margin-left: auto;
            margin-bottom: 5px;
        }
        .signature-text {
            font-size: 10px;
            color: #666;
        }
        
        /* Verification */
        .verification {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background: #f7fafc;
            border-radius: 5px;
        }
        .verification-code {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #2b6cb0;
        }
        .verification-text {
            font-size: 8px;
            color: #888;
            margin-top: 3px;
        }
        
        /* Warning Box */
        .warning-box {
            background: #fff5f5;
            border: 1px solid #fc8181;
            border-radius: 5px;
            padding: 10px;
            margin-top: 15px;
            font-size: 9px;
            color: #c53030;
        }
        
        /* Page break for printing */
        @media print {
            .prescription-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="prescription-container">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                <div class="clinic-name">{{ $clinicName }}</div>
                <div class="clinic-address">
                    {{ $clinicAddress }}<br>
                    Phone: {{ $clinicPhone }} | Email: {{ $clinicEmail }}
                </div>
            </div>
            
            <div class="doctor-info">
                <div class="doctor-details">
                    <div class="doctor-name">Dr. {{ $doctor->name }}</div>
                    <div class="doctor-quals">
                        {{ $doctorProfile->qualification ?? 'MBBS' }}
                        @if($doctorProfile && $doctorProfile->specialization)
                            | {{ $doctorProfile->specialization }}
                        @endif
                    </div>
                    <div class="doctor-reg">
                        Reg. No: {{ $doctorProfile->registration_number ?? 'MCI-XXXXX' }}
                    </div>
                </div>
                <div class="rx-info">
                    <div class="rx-date">Date: {{ $prescription->created_at->format('d M Y') }}</div>
                    <div class="rx-number">Rx #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Patient Information -->
        <div class="patient-section">
            <div class="patient-row">
                <div class="patient-cell">
                    <div class="patient-label">Patient Name</div>
                    <div class="patient-value">{{ $patient->name }}</div>
                </div>
                <div class="patient-cell">
                    <div class="patient-label">Age / Gender</div>
                    <div class="patient-value">{{ $patient->age ?? 'N/A' }} yrs / {{ ucfirst($patient->gender ?? 'N/A') }}</div>
                </div>
                <div class="patient-cell">
                    <div class="patient-label">Phone</div>
                    <div class="patient-value">{{ $patient->phone ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="patient-row" style="margin-top: 5px;">
                <div class="patient-cell" style="width: 100%;">
                    <div class="patient-label">Address</div>
                    <div class="patient-value">{{ $patient->address ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Diagnosis -->
        @if($visit->diagnosis)
        <div class="section-box">
            <div class="section-title">Diagnosis</div>
            <div class="section-content">{{ $visit->diagnosis }}</div>
        </div>
        @endif
        
        <!-- RX Symbol and Medicines -->
        <div class="rx-symbol">℞</div>
        
        <table class="medicines-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Medicine</th>
                    <th style="width: 20%;">Dosage</th>
                    <th style="width: 15%;">Duration</th>
                    <th style="width: 25%;">Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="medicine-name">{{ $item->medicine->name ?? $item->medicine_name }}</div>
                        @if($item->medicine && $item->medicine->generic_name)
                            <div class="medicine-generic">({{ $item->medicine->generic_name }})</div>
                        @endif
                    </td>
                    <td>
                        <span class="dosage-instructions">{{ $item->dosage ?? 'As directed' }}</span>
                    </td>
                    <td>{{ $item->duration ?? '-' }}</td>
                    <td>{{ $item->instructions ?? 'As directed by physician' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Notes/Advice -->
        @if($prescription->notes || $visit->notes)
        <div class="section-box">
            <div class="section-title">Special Instructions / Advice</div>
            <div class="section-content">
                {{ $prescription->notes ?? $visit->notes }}
            </div>
        </div>
        @endif
        
        <!-- Warning Box -->
        <div class="warning-box">
            <strong>⚠ Important:</strong> This prescription is valid for 7 days only. Please complete the full course of medication. 
            If symptoms persist or worsen, consult immediately. Keep all medicines out of reach of children.
        </div>
        
        <!-- Footer with Signature -->
        <div class="footer">
            <div class="signature-section">
                <div class="follow-up">
                    @if($visit->follow_up_date)
                    <div class="follow-up-text">
                        <strong>Follow-up Date:</strong> {{ \Carbon\Carbon::parse($visit->follow_up_date)->format('d M Y') }}
                    </div>
                    @endif
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-text">Doctor's Signature</div>
                </div>
            </div>
        </div>
        
        <!-- Verification Code -->
        <div class="verification">
            <div class="verification-code">{{ $verificationCode }}</div>
            <div class="verification-text">
                Verification Code - Use at {{ url('/prescription/verify/' . $prescription->id) }}
            </div>
        </div>
    </div>
</body>
</html>
