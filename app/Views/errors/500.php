<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
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
            border-bottom: 2px solid #ef4444;
            padding: 15px 20px;
            text-align: center;
        }

        .terminal-title {
            font-size: 16px;
            font-weight: bold;
            color: #ef4444;
            letter-spacing: 3px;
        }

        .terminal-body {
            padding: 60px 30px;
            background: #0d1117;
            text-align: center;
        }

        .error-code {
            color: #ef4444;
            font-size: 120px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }

        .error-message {
            color: #c9d1d9;
            font-size: 24px;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        .error-description {
            color: #8b949e;
            font-size: 14px;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: #161b22;
            border: 2px solid #4ade80;
            color: #4ade80;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-home:hover {
            background: #4ade80;
            color: #0d1117;
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
            <div class="terminal-title">ZEngine Error Handler</div>
        </div>

        <div class="terminal-body">
            <div class="error-code">500</div>
            <div class="error-message">Internal Server Error</div>
            <div class="error-description">
                Oops! Something went wrong on our end.<br>
                We're working to fix the issue. Please try again later.<br>
                <span class="cursor">▮</span>
            </div>
            <a href="/" class="btn-home">← Back to Home</a>
        </div>

        <div class="footer">
            ZEngine Framework │ Error 500 │ <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>

    <script>
        console.log('%c500 - Internal Server Error', 'color: #ef4444; font-size: 16px; font-weight: bold;');
    </script>
</body>
</html>
