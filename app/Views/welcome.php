<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZEngine - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            max-width: 900px;
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
            padding: 40px 30px;
            background: #0d1117;
        }

        .ascii-art {
            color: #4ade80;
            font-size: 12px;
            line-height: 1.2;
            margin-bottom: 30px;
            text-align: center;
            white-space: pre;
            font-family: monospace;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-text h1 {
            color: #4ade80;
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .welcome-text .version {
            color: #8b949e;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-left: 3px solid #4ade80;
            padding: 15px;
            border-radius: 4px;
        }

        .info-card-title {
            color: #4ade80;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-card-value {
            color: #c9d1d9;
            font-size: 16px;
            font-weight: bold;
        }

        .endpoints {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .endpoints-title {
            color: #fbbf24;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .endpoint-item {
            padding: 10px;
            background: #0d1117;
            border-left: 3px solid #60a5fa;
            margin-bottom: 10px;
            border-radius: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .endpoint-item:hover {
            background: #21262d;
            cursor: pointer;
        }

        .endpoint-method {
            color: #4ade80;
            font-weight: bold;
            width: 60px;
        }

        .endpoint-path {
            color: #60a5fa;
            flex: 1;
        }

        .endpoint-desc {
            color: #8b949e;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #30363d;
            background: #161b22;
            color: #8b949e;
            font-size: 12px;
        }

        .footer a {
            color: #4ade80;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
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
            <div class="terminal-title">ZEngine Framework</div>
        </div>

        <div class="terminal-body">
            <div class="ascii-art">
  _____     _____             _            
 |__  /    | ____|_ __   __ _(_)_ __   ___ 
   / /_____|  _| | '_ \ / _` | | '_ \ / _ \
  / /|_____| |___| | | | (_| | | | | |  __/
 /____|    |_____|_| |_|\__, |_|_| |_|\___|
                        |___/              
            </div>

            <div class="welcome-text">
                <h1>Welcome to ZEngine</h1>
                <p class="version">Version <?= $version ?? '1.0.0' ?> <span class="cursor">▮</span></p>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-title">► Services Loaded</div>
                    <div class="info-card-value"><?= $services_count ?? 12 ?> Services</div>
                </div>
                <div class="info-card">
                    <div class="info-card-title">► PHP Version</div>
                    <div class="info-card-value">PHP <?= PHP_VERSION ?></div>
                </div>
                <div class="info-card">
                    <div class="info-card-title">► Status</div>
                    <div class="info-card-value" style="color: #4ade80;">● Online</div>
                </div>
            </div>
        </div>

        <div class="footer">
            ZEngine Framework v<?= $version ?? '1.0.0' ?> │
            Built with ❤️ by <a href="https://github.com/spexdw">spexdw</a> │
            Licensed under MIT
        </div>
    </div>

    <script>
        console.log('%c⚡ ZEngine Framework', 'color: #4ade80; font-size: 20px; font-weight: bold;');
        console.log('%cVersion: <?= $version ?? '1.0.0' ?>', 'color: #60a5fa; font-size: 14px;');
        console.log('%cServices: <?= $services_count ?? 12 ?>', 'color: #fbbf24; font-size: 14px;');
        console.log('%cFramework initialized successfully!', 'color: #4ade80; font-size: 12px;');
    </script>
</body>
</html>
