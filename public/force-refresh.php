<?php
// Force clear all caches and reload
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$results = [];

// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    $results[] = "✅ OPcache cleared";
} else {
    $results[] = "⚠️ OPcache not enabled";
}

// Clear APCu cache
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    $results[] = "✅ APCu cache cleared";
}

// Run Laravel cache clear commands
$laravelCommands = [
    'php artisan view:clear',
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan route:clear'
];

foreach ($laravelCommands as $cmd) {
    exec("cd /var/www && $cmd 2>&1", $output, $returnCode);
    if ($returnCode === 0) {
        $results[] = "✅ " . str_replace('php artisan ', '', $cmd);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Refresh - ClinicFlow</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #1a1a1a;
            margin: 0 0 20px 0;
            font-size: 28px;
        }
        .result {
            padding: 12px 16px;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 14px;
        }
        .instructions {
            background: #fffbeb;
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        .instructions h3 {
            margin: 0 0 15px 0;
            color: #92400e;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
            color: #78350f;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        kbd {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Cache Cleared Successfully!</h1>
        
        <?php foreach ($results as $result): ?>
            <div class="result"><?= $result ?></div>
        <?php endforeach; ?>
        
        <div class="instructions">
            <h3>⚠️ Important: Clear Your Browser Cache</h3>
            <p>Server caches are cleared. Now clear your browser cache:</p>
            <ol>
                <li><strong>Chrome/Brave/Edge:</strong> Press <kbd>Cmd+Shift+R</kbd> (Mac) or <kbd>Ctrl+Shift+R</kbd> (Windows/Linux)</li>
                <li><strong>Safari:</strong> Press <kbd>Cmd+Option+E</kbd> then <kbd>Cmd+R</kbd></li>
                <li><strong>Firefox:</strong> Press <kbd>Cmd+Shift+Delete</kbd> or <kbd>Ctrl+Shift+Delete</kbd></li>
            </ol>
            <p style="margin-top: 15px; color: #92400e;"><strong>Or</strong> open Developer Tools (<kbd>F12</kbd>), right-click the refresh button, and select "Empty Cache and Hard Reload"</p>
        </div>
        
        <a href="/reception/dashboard" class="btn">← Back to Dashboard</a>
        <a href="/reception/pharmacy" class="btn" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); margin-left: 10px;">Go to Pharmacy</a>
    </div>
    
    <script>
        // Force browser to not cache this page
        window.onload = function() {
            if (window.performance && window.performance.navigation.type == window.performance.navigation.TYPE_BACK_FORWARD) {
                window.location.reload();
            }
        }
    </script>
</body>
</html>
