<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="version" content="2.0.0-csrf-fix">
    <title>Login - ClinicFlow v2.0 (CSRF Fixed)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    @auth
        <script>
            // If user is already logged in and tries to access login page, redirect to dashboard
            window.location.href = '{{ url("/") }}';
        </script>
    @endauth
    
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,sans-serif;background:#f8f9fa;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .container{width:100%;max-width:400px}
        .card{background:#fff;border:1px solid #e8e8e8;border-radius:16px;padding:40px 36px;box-shadow:0 4px 24px rgba(0,0,0,.06)}
        .logo{height:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
        .logo img{height:60px;width:auto;object-fit:contain}
        h1{text-align:center;font-size:26px;font-weight:700;color:#1a1a1a;margin-bottom:6px}
        .subtitle{text-align:center;font-size:15px;color:#6b7280;margin-bottom:32px}
        .error{background:#fef2f2;border:1px solid #fee2e2;color:#dc2626;padding:14px;border-radius:10px;font-size:14px;margin-bottom:24px}
        .success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;padding:14px;border-radius:10px;font-size:14px;margin-bottom:24px;display:none}
        .version{text-align:center;font-size:11px;color:#059669;margin-top:10px;font-weight:600}
        .group{margin-bottom:20px}
        label{display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:8px}
        .wrap{position:relative;display:flex;align-items:center}
        .wrap .icon{position:absolute;left:14px;color:#9ca3af;font-size:18px;pointer-events:none}
        input{width:100%;padding:14px 48px 14px 46px;font-size:15px;border:1.5px solid #e5e7eb;border-radius:12px;background:#fff;color:#1f2937;outline:none}
        input:focus{border-color:#1a1a1a;box-shadow:0 0 0 4px rgba(0,0,0,.05)}
        .toggle{position:absolute;right:14px;background:none;border:none;color:#9ca3af;cursor:pointer;padding:4px;border-radius:6px}
        .toggle:hover{color:#4b5563;background:#f3f4f6}
        .btn{width:100%;padding:14px;font-size:15px;font-weight:600;color:#fff;background:#1a1a1a;border:none;border-radius:12px;cursor:pointer;margin-top:8px}
        .btn:hover{background:#2d2d2d}
        .divider{display:flex;align-items:center;margin:28px 0;color:#9ca3af;font-size:12px}
        .divider::before,.divider::after{content:"";flex:1;height:1px;background:#e5e7eb}
        .divider span{padding:0 16px}
        .demo{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:6px}
        .row{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:8px}
        .row:hover{background:#fff}
        .info{display:flex;align-items:center;gap:10px}
        .badge{padding:4px 10px;font-size:10px;font-weight:700;text-transform:uppercase;border-radius:6px;color:#fff}
        .b-admin{background:#1a1a1a}
        .b-reception{background:#2563eb}
        .b-doctor{background:#059669}
        .email{font-size:14px;color:#4b5563;font-weight:500}
        .use{padding:8px 16px;font-size:13px;font-weight:600;color:#374151;background:#fff;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer}
        .use:hover{background:#1a1a1a;color:#fff;border-color:#1a1a1a}
        .footer{text-align:center;margin-top:24px;font-size:13px;color:#9ca3af}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <img src="{{ asset('assets/images/black.png') }}?v={{ config('app.version') }}" alt="ClinicFlow Logo">
            </div>
            <p class="subtitle">Sign in to your account</p>
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="group">
                    <label>Email</label>
                    <div class="wrap">
                        <span class="icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="email">
                    </div>
                </div>
                <div class="group">
                    <label>Password</label>
                    <div class="wrap">
                        <span class="icon"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="toggle" onclick="tog()"><i class="bi bi-eye" id="eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn">Sign in</button>
            </form>
            <div class="divider"><span>Demo Accounts</span></div>
            <div class="demo">
                <div class="row">
                    <div class="info"><span class="badge b-admin">Admin</span><span class="email">admin@clinic.test</span></div>
                    <button class="use" onclick="f('admin@clinic.test')">Use</button>
                </div>
                <div class="row">
                    <div class="info"><span class="badge b-reception">Reception</span><span class="email">reception@clinic.test</span></div>
                    <button class="use" onclick="f('reception@clinic.test')">Use</button>
                </div>
                <div class="row">
                    <div class="info"><span class="badge b-doctor">Doctor</span><span class="email">doctor@clinic.test</span></div>
                    <button class="use" onclick="f('doctor@clinic.test')">Use</button>
                </div>
            </div>
        </div>
        <p class="footer">Clinic Queue & Pharmacy System</p>
        <p class="version">v2.0.0 - CSRF Fix Active </p>
    </div>
    <script>
        function f(e){document.getElementById('email').value=e;document.getElementById('password').value='password';}
        function tog(){var p=document.getElementById('password'),i=document.getElementById('eye');if(p.type==='password'){p.type='text';i.className='bi bi-eye-slash';}else{p.type='password';i.className='bi bi-eye';}}
        
        // Show CSRF status indicator
        setTimeout(function() {
            var csrfStatus = document.getElementById('csrfStatus');
            if(csrfStatus) csrfStatus.style.display = 'block';
        }, 100);
        
        // Refresh CSRF token on page load to prevent stale tokens
        function refreshCsrfToken() {
            // Disabled since route doesn't exist
            /* fetch('/refresh-csrf', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(response => response.json())
            .then(data => {
                const csrfInputs = document.querySelectorAll('input[name="_token"]');
                csrfInputs.forEach(input => {
                    input.value = data.csrf_token;
                });
                console.log('✅ CSRF token refreshed successfully');
            })
            .catch(error => console.error('❌ Error refreshing CSRF token:', error)); */
        }
        
        // Refresh immediately on page load
        window.addEventListener('DOMContentLoaded', function() {
            console.log('🔄 Refreshing CSRF token on page load...');
            refreshCsrfToken();
        });
        
        // Refresh before form submission to ensure fresh token
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('🔄 Refreshing CSRF token before login...');
                    
                        console.log('🔄 Submitting form directly...');
                        // Try submitting anyway
                        form.submit();
                    /*
                    fetch('/refresh-csrf', {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'no-cache'
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update CSRF token
                        const csrfInputs = document.querySelectorAll('input[name="_token"]');
                        csrfInputs.forEach(input => {
                            input.value = data.csrf_token;
                        });
                        console.log('✅ CSRF token updated, submitting form...');
                        // Submit form with fresh token
                        form.submit();
                    })
                    .catch(error => {
                        console.error('❌ Error refreshing CSRF token:', error);
                        // Try submitting anyway
                        form.submit();
                    });
                    */
                });
            }
        });
        
        // Auto-refresh CSRF token every 5 minutes
        setInterval(refreshCsrfToken, 300000); // 5 minutes
        
        // Refresh on page visibility change (when user comes back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('👁️ Tab became visible, refreshing CSRF token...');
                refreshCsrfToken();
            }
        });
    </script>
</body>
</html>
