<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZEngine Debug Console</title>
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
            max-width: 1400px;
            margin: 0 auto;
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
            letter-spacing: 2px;
        }

        .container {
            padding: 20px;
            background: #0d1117;
        }

        .error-banner {
            background: linear-gradient(90deg, #2d1517 0%, #3d1f21 50%, #2d1517 100%);
            border-top: 3px solid #ef4444;
            border-bottom: 3px solid #ef4444;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .error-banner h1 {
            color: #ef4444;
            font-size: 32px;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-type {
            background: #ef4444;
            color: #ffffff;
            padding: 8px 25px;
            border-radius: 4px;
            display: inline-block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .info-box {
            background: #161b22;
            border: 1px solid #30363d;
            border-left: 4px solid #4ade80;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .info-box-header {
            background: #21262d;
            border-bottom: 1px solid #30363d;
            padding: 12px 15px;
            color: #4ade80;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-box-content {
            padding: 15px;
            background: #0d1117;
        }

        .error-message {
            color: #f87171;
            font-size: 15px;
            line-height: 1.6;
            word-wrap: break-word;
            font-weight: 500;
        }

        .file-location {
            margin-top: 15px;
            padding: 12px;
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 4px;
        }

        .file-location-label {
            color: #fbbf24;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .file-path {
            color: #60a5fa;
            font-size: 13px;
            word-break: break-all;
        }

        .line-number {
            color: #c084fc;
            font-weight: bold;
        }

        .code-viewer {
            background: #0d1117;
            border: 2px solid #30363d;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .code-header {
            background: #21262d;
            padding: 12px 15px;
            border-bottom: 2px solid #4ade80;
            color: #4ade80;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
        }

        .code-content {
            background: #0d1117;
            padding: 0;
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }

        .code-content::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        .code-content::-webkit-scrollbar-track {
            background: #161b22;
        }

        .code-content::-webkit-scrollbar-thumb {
            background: #4ade80;
            border-radius: 6px;
        }

        .code-line {
            display: flex;
            font-size: 14px;
            line-height: 1.6;
            padding: 6px 0;
            border-left: 4px solid transparent;
        }

        .code-line:hover {
            background: #161b22;
        }

        .code-line-num {
            display: inline-block;
            width: 60px;
            text-align: right;
            color: #6e7681;
            padding-right: 15px;
            user-select: none;
            flex-shrink: 0;
            border-right: 1px solid #30363d;
            margin-right: 15px;
            font-weight: 500;
        }

        .code-line-content {
            color: #c9d1d9;
            white-space: pre;
            flex: 1;
            padding-right: 15px;
        }

        .error-line {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
        }

        .error-line .code-line-num {
            color: #ef4444;
            font-weight: bold;
        }

        .error-line .code-line-content {
            color: #fca5a5;
            font-weight: 600;
        }

        .stack-trace {
            background: #0d1117;
            border: 2px solid #30363d;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .stack-header {
            background: #21262d;
            padding: 12px 15px;
            border-bottom: 2px solid #fbbf24;
            color: #fbbf24;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
        }

        .stack-content {
            padding: 15px;
            background: #0d1117;
            max-height: 400px;
            overflow-y: auto;
        }

        .stack-content::-webkit-scrollbar {
            width: 12px;
        }

        .stack-content::-webkit-scrollbar-track {
            background: #161b22;
        }

        .stack-content::-webkit-scrollbar-thumb {
            background: #fbbf24;
            border-radius: 6px;
        }

        .stack-item {
            margin-bottom: 12px;
            padding: 12px;
            background: #161b22;
            border-left: 3px solid #fbbf24;
            font-size: 13px;
            line-height: 1.6;
            border-radius: 4px;
        }

        .stack-item:hover {
            background: #1c2128;
            border-left-color: #fcd34d;
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
            color: #4ade80;
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
    </style>
</head>
<body>
    <div class="main-container">
        <div class="terminal-header">
            <div class="terminal-title">ZEngine Debug Console v1.0</div>
        </div>

        <div class="container">
            <div class="error-banner">
                <h1>⚠ FATAL ERROR ⚠</h1>
                <div class="error-type"><?= get_class($e) ?></div>
            </div>

            <div class="info-box">
                <div class="info-box-header">
                    ► Error Message
                </div>
                <div class="info-box-content">
                    <div class="error-message"><?= htmlspecialchars($message) ?></div>

                    <div class="file-location">
                        <div class="file-location-label">► File Location:</div>
                        <div class="file-path"><?= htmlspecialchars($file) ?></div>
                        <div style="margin-top: 8px;">
                            <span class="file-location-label">► Line:</span>
                            <span class="line-number"><?= $line ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($code)): ?>
            <div class="code-viewer">
                <div class="code-header">
                    ► Source Code [Lines <?= min(array_keys($code)) ?> - <?= max(array_keys($code)) ?>]
                </div>
                <div class="code-content">
                    <?php foreach ($code as $num => $content): ?>
                    <div class="code-line <?= $num === $line ? 'error-line' : '' ?>">
                        <span class="code-line-num"><?= $num ?></span>
                        <span class="code-line-content"><?= htmlspecialchars($content) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="stack-trace">
                <div class="stack-header">
                    ► Stack Trace
                </div>
                <div class="stack-content">
                    <?php
                    $traceLines = explode("\n", $trace);
                    foreach ($traceLines as $index => $traceLine):
                        if (trim($traceLine)):
                    ?>
                    <div class="stack-item">
                        <div><span style="color: #6e7681;">#{<?= $index ?>}</span> <?= htmlspecialchars($traceLine) ?></div>
                    </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>

            <div class="footer">
                ZEngine Framework Debug Console │
                <span class="blink">●</span> DEVELOPMENT MODE │
                Powered by <a href="https://github.com/spexdw/z-engine">ZEngine</a> │
                <?= date('Y-m-d H:i:s') ?>
            </div>
        </div>
    </div>

    <script>
        console.log('%c⚠ FATAL ERROR DETECTED', 'color: #ef4444; font-size: 20px; font-weight: bold;');
        console.log('%cError: <?= addslashes($message) ?>', 'color: #f87171; font-size: 14px;');
        console.log('%cFile: <?= addslashes($file) ?>:<?= $line ?>', 'color: #60a5fa; font-size: 12px;');

        document.addEventListener('DOMContentLoaded', function() {
            const errorLine = document.querySelector('.error-line');
            if (errorLine) {
                errorLine.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
