<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - ZEngine Security</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            background: #0d1117;
            color: #c9d1d9;
            line-height: 1.5;
            padding: 20px;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #161b22;
            border: 2px solid #30363d;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .terminal-header {
            background: linear-gradient(180deg, #21262d 0%, #161b22 100%);
            border-bottom: 2px solid #ef4444;
            padding: 15px 20px;
            text-align: center;
        }

        .terminal-title {
            font-size: 16px;
            font-weight: bold;
            color: #ef4444;
            letter-spacing: 2px;
        }

        .container {
            padding: 40px 20px;
            background: #0d1117;
            text-align: center;
        }

        .error-banner {
            background: linear-gradient(90deg, #2d1517 0%, #3d1f21 50%, #2d1517 100%);
            border-top: 3px solid #ef4444;
            border-bottom: 3px solid #ef4444;
            padding: 40px 25px;
            margin-bottom: 30px;
            border-radius: 6px;
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .error-banner h1 {
            color: #ef4444;
            font-size: 48px;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-code {
            background: #ef4444;
            color: #ffffff;
            padding: 10px 30px;
            border-radius: 4px;
            display: inline-block;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .info-section {
            background: #161b22;
            border: 2px solid #30363d;
            border-left: 4px solid #ef4444;
            margin-bottom: 30px;
            border-radius: 6px;
            padding: 25px;
            text-align: left;
        }

        .info-title {
            color: #ef4444;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .info-description {
            color: #c9d1d9;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .blocked-path {
            background: #0d1117;
            border: 1px solid #30363d;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .blocked-path-label {
            color: #fbbf24;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .blocked-path-value {
            color: #f87171;
            font-size: 14px;
            word-break: break-all;
            font-weight: 600;
        }

        .reasons-list {
            list-style: none;
            padding: 0;
        }

        .reasons-list li {
            padding: 12px 15px;
            margin-bottom: 10px;
            background: #0d1117;
            border-left: 3px solid #fbbf24;
            border-radius: 4px;
            font-size: 14px;
        }

        .reasons-list li:before {
            content: "►";
            color: #fbbf24;
            font-weight: bold;
            margin-right: 10px;
        }

        .action-buttons {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #4ade80;
            color: #0d1117;
        }

        .btn-primary:hover {
            background: #22c55e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.4);
        }

        .btn-secondary {
            background: #30363d;
            color: #c9d1d9;
        }

        .btn-secondary:hover {
            background: #484f58;
            transform: translateY(-2px);
        }

        .footer {
            background: #161b22;
            border-top: 2px solid #30363d;
            padding: 15px 20px;
            text-align: center;
            color: #8b949e;
            font-size: 11px;
        }

        .footer a {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .blink {
            animation: blink 1s step-start infinite;
        }

        @keyframes blink {
            50% { opacity: 0; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-icon {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="terminal-header">
            <div class="terminal-title">ZEngine Security</div>
        </div>

        <div class="container">
            <div class="error-banner">
                <div class="error-icon">🚫</div>
                <h1>Access Denied</h1>
                <div class="error-code">HTTP 403 Forbidden</div>
            </div>

            <div class="info-section">
                <div class="info-title">► What Happened?</div>
                <div class="info-description">
                    <?php if (isset($message)): ?>
                        <?= htmlspecialchars($message) ?>
                    <?php else: ?>
                        You attempted to access a protected resource that is not available to the public.
                        This area contains sensitive framework files and configurations that are restricted for security reasons.
                    <?php endif; ?>
                </div>

                <div class="blocked-path">
                    <div class="blocked-path-label">► Requested Path:</div>
                    <div class="blocked-path-value"><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?></div>
                </div>

                <?php if (!isset($message)): ?>
                <div class="info-title" style="margin-top: 25px;">► Why Was I Blocked?</div>
                <ul class="reasons-list">
                    <li>The requested path contains sensitive framework files</li>
                    <li>Direct access to core system files is prohibited</li>
                    <li>This is a security measure to protect the application</li>
                    <li>Only authorized routes can be accessed through the router</li>
                </ul>
                <?php else: ?>
                <div class="info-title" style="margin-top: 25px;">► Need Access?</div>
                <ul class="reasons-list">
                    <li>Contact your administrator if you believe this is an error</li>
                    <li>Check your account permissions and role</li>
                    <li>Some categories and features are restricted by role</li>
                </ul>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
                <a href="/" class="btn btn-primary">Go to Home</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            </div>
        </div>

        <div class="footer">
            ZEngine Security │
            <span class="blink">●</span> PROTECTION ACTIVE │
            IP: <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown') ?> │
            <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>

    <script>
        console.log('%cACCESS DENIED', 'color: #ef4444; font-size: 20px; font-weight: bold;');
        console.log('%cPath: <?= addslashes($_SERVER['REQUEST_URI'] ?? '/') ?>', 'color: #f87171; font-size: 14px;');
        console.log('%cReason: Protected resource', 'color: #fbbf24; font-size: 12px;');
    </script>
</body>
</html>
