<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Prescription - {{ $patient->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #f5f5f5;
        }
        .print-only {
            display: none;
        }
        .no-print {
            display: block;
        }
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block;
            }
            .prescription-container {
                box-shadow: none;
                margin: 0;
                padding: 15px;
            }
        }
        
        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .print-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-print {
            background: #10b981;
            color: white;
        }
        .btn-close {
            background: #6b7280;
            color: white;
        }
        
        /* Main Container */
        .prescription-container {
            max-width: 800px;
            margin: 70px auto 20px;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #1a365d;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .clinic-info {
            text-align: center;
            margin-bottom: 15px;
        }
        .clinic-name {
            font-size: 24px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .clinic-address {
            font-size: 11px;
            color: #666;
        }
        .doctor-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 15px;
        }
        .doctor-details {}
        .doctor-name {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }
        .doctor-quals {
            font-size: 11px;
            color: #666;
        }
        .doctor-reg {
            font-size: 10px;
            color: #888;
        }
        .rx-info {
            text-align: right;
        }
        .rx-date {
            font-size: 11px;
            color: #666;
        }
        .rx-number {
            font-size: 14px;
            font-weight: 700;
            color: #2b6cb0;
        }
        
        /* Patient Section */
        .patient-section {
            background: #f7fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .patient-field {
            min-width: 150px;
        }
        .patient-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
        }
        .patient-value {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
        }
        
        /* Diagnosis */
        .diagnosis-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .diagnosis-label {
            font-size: 10px;
            font-weight: 600;
            color: #856404;
            text-transform: uppercase;
        }
        .diagnosis-text {
            font-size: 13px;
            color: #333;
            margin-top: 3px;
        }
        
        /* RX Symbol */
        .rx-symbol {
            font-size: 40px;
            font-weight: bold;
            color: #2b6cb0;
            font-family: 'Times New Roman', serif;
            margin: 10px 0;
        }
        
        /* Medicines */
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .medicines-table th {
            background: #2d3748;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .medicines-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .medicines-table tr:hover {
            background: #f7fafc;
        }
        .medicine-name {
            font-weight: 600;
            font-size: 13px;
            color: #2d3748;
        }
        .medicine-generic {
            font-size: 10px;
            color: #888;
            font-style: italic;
        }
        .dosage {
            color: #dc2626;
            font-weight: 700;
            font-size: 13px;
        }
        
        /* Notes */
        .notes-box {
            background: #f0fff4;
            border-left: 4px solid #10b981;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .notes-label {
            font-size: 10px;
            font-weight: 600;
            color: #047857;
            text-transform: uppercase;
        }
        .notes-text {
            font-size: 12px;
            color: #333;
            margin-top: 3px;
        }
        
        /* Warning */
        .warning-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #b91c1c;
        }
        
        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .follow-up {
            font-size: 12px;
            color: #2d3748;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            width: 180px;
            border-top: 2px solid #333;
            margin-bottom: 5px;
        }
        .signature-text {
            font-size: 11px;
            color: #666;
        }
        
        /* Verification */
        .verification {
            text-align: center;
            margin-top: 25px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
        }
        .verification-code {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #2b6cb0;
            font-family: monospace;
        }
        .verification-text {
            font-size: 9px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Print Controls (hidden when printing) -->
    <div class="print-controls no-print">
        <span>Prescription Preview</span>
        <div>
            <button class="btn-print" onclick="window.print()">
                🖨️ Print Prescription
            </button>
            <button class="btn-close" onclick="window.close()">
                ✕ Close
            </button>
        </div>
    </div>
    
    <div class="prescription-container">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                <div class="clinic-name">{{ $clinicName }}</div>
                <div class="clinic-address">
                    {{ $clinicAddress }}<br>
                    📞 {{ $clinicPhone }} | ✉ {{ $clinicEmail }}
                </div>
            </div>
            
            <div class="doctor-row">
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
                    <div class="rx-date">{{ $prescription->created_at->format('l, d F Y') }}</div>
                    <div class="rx-number">Rx #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Patient Information -->
        <div class="patient-section">
            <div class="patient-field">
                <div class="patient-label">Patient Name</div>
                <div class="patient-value">{{ $patient->name }}</div>
            </div>
            <div class="patient-field">
                <div class="patient-label">Age</div>
                <div class="patient-value">{{ $patient->age ?? 'N/A' }} years</div>
            </div>
            <div class="patient-field">
                <div class="patient-label">Gender</div>
                <div class="patient-value">{{ ucfirst($patient->gender ?? 'N/A') }}</div>
            </div>
            <div class="patient-field">
                <div class="patient-label">Phone</div>
                <div class="patient-value">{{ $patient->phone ?? 'N/A' }}</div>
            </div>
            <div class="patient-field" style="flex: 1;">
                <div class="patient-label">Address</div>
                <div class="patient-value">{{ $patient->address ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Diagnosis -->
        @if($visit->diagnosis)
        <div class="diagnosis-box">
            <div class="diagnosis-label">Diagnosis</div>
            <div class="diagnosis-text">{{ $visit->diagnosis }}</div>
        </div>
        @endif
        
        <!-- RX Symbol and Medicines -->
        <div class="rx-symbol">℞</div>
        
        <table class="medicines-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Medicine</th>
                    <th style="width: 18%;">Dosage</th>
                    <th style="width: 12%;">Duration</th>
                    <th style="width: 35%;">Instructions</th>
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
                        <span class="dosage">{{ $item->dosage ?? 'As directed' }}</span>
                    </td>
                    <td>{{ $item->duration ?? '-' }}</td>
                    <td>{{ $item->instructions ?? 'As directed by physician' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Notes/Advice -->
        @if($prescription->notes || $visit->notes)
        <div class="notes-box">
            <div class="notes-label">Special Instructions / Advice</div>
            <div class="notes-text">{{ $prescription->notes ?? $visit->notes }}</div>
        </div>
        @endif
        
        <!-- Warning Box -->
        <div class="warning-box">
            <strong>⚠️ Important:</strong> This prescription is valid for 7 days only. Please complete the full course of medication. 
            If symptoms persist or worsen, consult immediately. Keep all medicines out of reach of children. 
            Do not self-medicate or share prescription medicines with others.
        </div>
        
        <!-- Footer with Signature -->
        <div class="footer">
            <div class="follow-up">
                @if($visit->follow_up_date)
                    <strong>📅 Follow-up Date:</strong> {{ \Carbon\Carbon::parse($visit->follow_up_date)->format('l, d F Y') }}
                @else
                    <em>Follow-up: As needed</em>
                @endif
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-text">Doctor's Signature & Stamp</div>
            </div>
        </div>
        
        <!-- Verification Code -->
        <div class="verification">
            <div class="verification-code">{{ $verificationCode }}</div>
            <div class="verification-text">
                Prescription Verification Code | Verify at: {{ url('/prescription/verify/' . $prescription->id) }}
            </div>
        </div>
    </div>
    
    <script>
        // Auto print if in print mode
        @if(isset($printMode) && $printMode)
            window.onload = function() {
                window.print();
            };
        @endif
    </script>
</body>
</html>
