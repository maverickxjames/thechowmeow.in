<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --violet: #7c3aed;
            --violet-light: #8b5cf6;
            --violet-dark: #6d28d9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0c1a;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated gradient orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #7c3aed; top: -150px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: #db2777; bottom: -100px; right: -100px; animation-delay: -3s; }
        .orb-3 { width: 300px; height: 300px; background: #0ea5e9; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid overlay */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,58,237,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,58,237,0.07) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }

        /* Gear icon with spin */
        .icon-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto 2rem;
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .icon-wrap::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px dashed rgba(124,58,237,0.4);
            animation: spin 12s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .icon-wrap svg {
            animation: spin-slow 8s linear infinite;
        }
        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(124,58,237,0.2);
            border: 1px solid rgba(124,58,237,0.4);
            color: #a78bfa;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 1.5rem;
        }
        .badge .dot {
            width: 6px;
            height: 6px;
            background: #a78bfa;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 30%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            font-size: 1.0625rem;
            color: #9ca3af;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        /* Progress bar */
        .progress-wrap {
            background: rgba(255,255,255,0.06);
            border-radius: 100px;
            height: 4px;
            overflow: hidden;
            margin-bottom: 2.5rem;
        }
        .progress-bar {
            height: 100%;
            border-radius: 100px;
            background: linear-gradient(90deg, var(--violet), #db2777, var(--violet));
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
            width: 70%;
        }
        @keyframes shimmer {
            0% { background-position: 200% center; }
            100% { background-position: -200% center; }
        }

        /* Info cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 2.5rem;
        }
        .card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 16px;
            backdrop-filter: blur(10px);
        }
        .card-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        .card-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 2px;
        }
        .card-value {
            font-size: 13px;
            font-weight: 600;
            color: #e5e7eb;
        }

        /* App name */
        .app-name {
            font-size: 13px;
            color: #4b5563;
            font-weight: 500;
        }
        .app-name span {
            color: #7c3aed;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="container">

        <div class="icon-wrap">
            <svg width="40" height="40" fill="none" stroke="#a78bfa" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>

        <div class="badge">
            <span class="dot"></span>
            Maintenance in Progress
        </div>

        <h1>We'll Be Back<br>Very Soon!</h1>

        <p class="message">{{ $message }}</p>

        <div class="progress-wrap">
            <div class="progress-bar"></div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-icon">🛠️</div>
                <div class="card-label">Status</div>
                <div class="card-value">Upgrading</div>
            </div>
            <div class="card">
                <div class="card-icon">⚡</div>
                <div class="card-label">Progress</div>
                <div class="card-value">In Progress</div>
            </div>
            <div class="card">
                <div class="card-icon">📧</div>
                <div class="card-label">Support</div>
                <div class="card-value">Available</div>
            </div>
        </div>

        <p class="app-name">— <span>{{ config('app.name') }}</span> team</p>
    </div>
</body>
</html>
