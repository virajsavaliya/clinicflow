<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $bill->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .clinic-info {
            flex: 1;
        }
        .clinic-info h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .clinic-info p {
            margin: 3px 0;
            font-size: 11px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .invoice-meta {
            text-align: right;
            font-size: 11px;
        }
        .invoice-meta p {
            margin: 2px 0;
        }
        .patient-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .patient-info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #f0f0f0;
        }
        thead th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 2px solid #333;
        }
        tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-bottom: 20px;
            text-align: right;
        }
        .totals-table {
            margin-left: auto;
            width: 300px;
            font-size: 11px;
        }
        .totals-table tr td {
            padding: 5px 10px;
            border: none;
        }
        .totals-table tr.total-row {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-weight: bold;
        }
        .totals-table tr.total-row td {
            padding: 8px 10px;
        }
        .notes {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #2c3e50;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .notes-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="clinic-info">
                @if($doctorProfile && $doctorProfile->getClinicLogoUrl())
                    <img src="{{ public_path($doctorProfile->getClinicLogoUrl()) }}" alt="Logo" style="max-height: 40px; margin-bottom: 10px;">
                @endif
                <h2>{{ $doctorProfile?->clinic_name ?? 'Medical Clinic' }}</h2>
                <p><strong>Dr. {{ $bill->visit->doctor->name }}</strong></p>
            </div>
            <div class="invoice-title">
                <h3>INVOICE</h3>
                <div class="invoice-meta">
                    <p><strong>Invoice #:</strong> {{ $bill->id }}</p>
                    <p><strong>Date:</strong> {{ $bill->created_at->format('d M Y') }}</p>
                    <p>
                        <strong>Status:</strong> 
                        <span class="status-badge status-{{ $bill->status }}">{{ strtoupper($bill->status) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Patient Section -->
        <div class="patient-section">
            <div class="patient-info">
                <div class="section-title">Bill To</div>
                <p><strong>{{ $bill->visit->patient->name }}</strong></p>
                <p>Phone: {{ $bill->visit->patient->phone ?? 'N/A' }}</p>
                <p>Age: {{ $bill->visit->patient->age ?? 'N/A' }} | Gender: {{ $bill->visit->patient->gender ?? 'N/A' }}</p>
            </div>
            <div class="patient-info">
                <div class="section-title">Visit Information</div>
                <p><strong>Visit ID:</strong> {{ $bill->visit->id }}</p>
                <p><strong>Queue #:</strong> {{ $bill->visit->queue_number }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="width: 60px;" class="text-right">Qty</th>
                    <th style="width: 80px;" class="text-right">Unit Price</th>
                    <th style="width: 80px;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $item)
                <tr>
                    <td>
                        {{ $item->title }}
                        <br><span style="color: #999; font-size: 10px;">({{ ucfirst($item->item_type) }})</span>
                    </td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right"><strong>₹{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Medicine Total:</td>
                    <td class="text-right"><strong>₹{{ number_format($bill->medicine_total, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Doctor Charges:</td>
                    <td class="text-right"><strong>₹{{ number_format($bill->doctor_charges_total, 2) }}</strong></td>
                </tr>
                @if($bill->taxes)
                <tr>
                    <td>Taxes:</td>
                    <td class="text-right"><strong>₹{{ number_format($bill->taxes, 2) }}</strong></td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL AMOUNT:</td>
                    <td class="text-right">₹{{ number_format($bill->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid Amount:</td>
                    <td class="text-right"><strong>₹{{ number_format($bill->paid_amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Balance Due:</strong></td>
                    <td class="text-right"><strong>₹{{ number_format($bill->getBalanceDue(), 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($bill->visit->notes)
        <div class="notes">
            <div class="notes-title">DIAGNOSIS NOTES</div>
            <p>{{ $bill->visit->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your visit!</p>
            <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
