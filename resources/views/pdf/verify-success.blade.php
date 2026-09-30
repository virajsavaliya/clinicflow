<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Verified - ClinicFlow</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: #10b981;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .check-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: #10b981;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .body {
            padding: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-size: 13px;
        }
        .info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
            text-align: right;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .logo {
            font-weight: 700;
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="check-icon">✓</div>
            <h1>Prescription Verified</h1>
            <p>This prescription is authentic and valid</p>
        </div>
        <div class="body">
            <div class="info-row">
                <span class="info-label">Prescription ID</span>
                <span class="info-value">#{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Patient Name</span>
                <span class="info-value">{{ $patient->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Prescribed By</span>
                <span class="info-value">Dr. {{ $doctor->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date Issued</span>
                <span class="info-value">{{ $prescription->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value" style="color: #10b981;">✓ Authentic</span>
            </div>
        </div>
        <div class="footer">
            Verified by <span class="logo">ClinicFlow</span><br>
            This verification confirms the prescription was issued through our system.
        </div>
    </div>
</body>
</html>
