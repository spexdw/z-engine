<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', 'Consolas', Monaco, monospace;
            background: #0d1117;
            color: #c9d1d9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .terminal-container {
            max-width: 700px;
            width: 100%;
            background: #161b22;
            border: 2px solid #30363d;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .terminal-header {
            background: linear-gradient(180deg, #21262d 0%, #161b22 100%);
            border-bottom: 2px solid #4ade80;
            padding: 15px 20px;
            text-align: center;
        }

        .terminal-title {
            font-size: 16px;
            font-weight: bold;
            color: #4ade80;
            letter-spacing: 3px;
        }

        .terminal-body {
            padding: 60px 30px;
            background: #0d1117;
            text-align: center;
        }

        .maintenance-icon {
            color: #4ade80;
            font-size: 100px;
            margin-bottom: 30px;
            text-shadow: 0 0 20px rgba(74, 222, 128, 0.3);
        }

        .maintenance-title {
            color: #4ade80;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .maintenance-message {
            color: #c9d1d9;
            font-size: 16px;
            margin-bottom: 40px;
            line-height: 1.8;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .loading-bar {
            width: 200px;
            height: 4px;
            background: #30363d;
            margin: 0 auto;
            border-radius: 2px;
            overflow: hidden;
        }

        .loading-progress {
            height: 100%;
            background: #4ade80;
            width: 0;
            animation: loading 2s ease-in-out infinite;
        }

        @keyframes loading {
            0% { width: 0; }
            50% { width: 100%; }
            100% { width: 0; }
        }

        .footer {
            background: #161b22;
            border-top: 1px solid #30363d;
            padding: 15px 20px;
            text-align: center;
            color: #8b949e;
            font-size: 11px;
        }

        .footer a {
            color: #4ade80;
            text-decoration: none;
        }

        .cursor {
            animation: blink 1s step-start infinite;
        }

        @keyframes blink {
            50% { opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="terminal-container">
        <div class="terminal-header">
            <div class="terminal-title">ZENGINE</div>
        </div>

        <div class="terminal-body">
            <div class="maintenance-icon">⚙</div>
            <div class="maintenance-title">Maintenance Mode</div>
            <div class="maintenance-message">
                <?= htmlspecialchars($message ?? env('MAINTENANCE_MESSAGE', 'We are currently performing maintenance. Please check back soon.')) ?>
                <br><br>
                <span class="cursor">▮</span>
            </div>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>

        <div class="footer">
            ZEngine Framework │ Maintenance Mode │ <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>

    <script>
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
