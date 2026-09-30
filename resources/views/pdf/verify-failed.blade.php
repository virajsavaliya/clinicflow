<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed - ClinicFlow</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            background: #ef4444;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .x-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: #ef4444;
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
            text-align: center;
        }
        .warning-text {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .warning-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            color: #991b1b;
            font-size: 13px;
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
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="x-icon">✕</div>
            <h1>Verification Failed</h1>
            <p>This prescription could not be verified</p>
        </div>
        <div class="body">
            <p class="warning-text">
                The prescription you are trying to verify does not exist in our system 
                or may have been tampered with.
            </p>
            <div class="warning-box">
                <strong>⚠️ Warning:</strong> Do not accept this prescription. 
                Please contact the issuing clinic directly to verify authenticity.
            </div>
        </div>
        <div class="footer">
            <span class="logo">ClinicFlow</span> Verification System<br>
            If you believe this is an error, please contact support.
        </div>
    </div>
</body>
</html>
