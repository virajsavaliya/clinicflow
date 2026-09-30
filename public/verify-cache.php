<!DOCTYPE html>
<html>
<head>
    <title>Cache Verification - ClinicFlow</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 40px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .timestamp {
            color: #86efac;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .box {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .box h3 {
            margin: 0 0 15px 0;
            color: #86efac;
        }
        .status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
        }
        .status.ok::before {
            content: "✅";
            font-size: 20px;
        }
        .status.warning::before {
            content: "⚠️";
            font-size: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            margin: 10px 10px 10px 0;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn.secondary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        .code {
            background: #000;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
            color: #86efac;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Cache Verification</h1>
        <div class="timestamp">Current Time: <?= date('Y-m-d H:i:s') ?></div>
        
        <div class="box">
            <h3>File Deployment Status</h3>
            <?php
            $files = [
                '/var/www/resources/views/reception/dashboard.blade.php' => 'Dashboard',
                '/var/www/resources/views/reception/pharmacy.blade.php' => 'Pharmacy'
            ];
            
            foreach ($files as $file => $name):
                if (file_exists($file)):
                    $mtime = filemtime($file);
                    $age = time() - $mtime;
                    $class = $age < 300 ? 'ok' : 'warning'; // Less than 5 minutes
            ?>
                <div class="status <?= $class ?>">
                    <strong><?= $name ?>:</strong> 
                    Updated <?= date('H:i:s', $mtime) ?> 
                    (<?= $age ?> seconds ago)
                </div>
            <?php 
                else:
            ?>
                <div class="status warning">
                    <strong><?= $name ?>:</strong> File not found!
                </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        
        <div class="box">
            <h3>Verify Modal Fix</h3>
            <p>The pharmacy modal fix is deployed. Test it:</p>
            <ol style="line-height: 1.8;">
                <li>Go to Pharmacy page</li>
                <li>Click <strong>Edit</strong> or <strong>Stock</strong> button</li>
                <li>Click inside the form fields (name, price, quantity)</li>
                <li><strong>✅ Modal should STAY OPEN</strong></li>
                <li>Click outside the modal (dark area)</li>
                <li><strong>✅ Modal should CLOSE</strong></li>
            </ol>
        </div>
        
        <div class="box">
            <h3>Dashboard Modal Spacing</h3>
            <p>The "Add to Queue" modal has improved spacing:</p>
            <div class="code">margin: 0.75rem 0 0.5rem; (reduced from 1rem 0 0.75rem)</div>
            <p>No extra white space at the bottom.</p>
        </div>
        
        <div class="box">
            <h3>Browser Cache Instructions</h3>
            <p><strong>IMPORTANT:</strong> You must clear your browser cache to see changes!</p>
            <div class="code">
Mac: Cmd + Shift + R (hard refresh)<br>
Windows/Linux: Ctrl + Shift + R<br>
<br>
Or: F12 → Right-click refresh → "Empty Cache and Hard Reload"
            </div>
        </div>
        
        <a href="/force-refresh.php" class="btn">🔄 Force Refresh All Caches</a>
        <a href="/reception/pharmacy" class="btn secondary">Test Pharmacy Page</a>
        <a href="/reception/dashboard" class="btn secondary">Test Dashboard</a>
    </div>
</body>
</html>
