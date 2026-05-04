<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon | {{ $tenant->name ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0f1e;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }
        .bg-orb-1 { width: 500px; height: 500px; background: #16a34a; top: -150px; left: -150px; animation-delay: 0s; }
        .bg-orb-2 { width: 400px; height: 400px; background: #2563eb; bottom: -100px; right: -100px; animation-delay: -4s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }

        /* Stars */
        .stars {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1px 1px at 20% 30%, rgba(255,255,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 80% 10%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 50% 60%, rgba(255,255,255,0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 10% 80%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 90% 90%, rgba(255,255,255,0.4) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 65% 25%, rgba(255,255,255,0.5) 0%, transparent 100%);
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 560px;
        }

        .badge {
            display: inline-block;
            background: rgba(22,163,74,0.15);
            border: 1px solid rgba(22,163,74,0.4);
            color: #4ade80;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            animation: pulse-badge 2s ease-in-out infinite;
        }

        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 0 0 0 rgba(22,163,74,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(22,163,74,0); }
        }

        .icon {
            font-size: 5rem;
            display: block;
            margin-bottom: 1.5rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .tagline {
            font-size: 1.1rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .tenant-name {
            color: #4ade80;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #16a34a, #2563eb);
            border-radius: 2px;
            margin: 0 auto 2rem;
        }

        .notify-form {
            display: flex;
            gap: 0.5rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .notify-input {
            flex: 1;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .notify-input:focus { border-color: #16a34a; }
        .notify-input::placeholder { color: #475569; }

        .notify-btn {
            padding: 0.75rem 1.25rem;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .notify-btn:hover { background: #15803d; }

        .footer-note {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="stars"></div>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <div class="container">
        <div class="badge">⚡ Launching Soon</div>

        <span class="icon">🏗️</span>

        <h1>
            @if($tenant)
                <span class="tenant-name">{{ $tenant->name }}</span><br>is Getting Ready
            @else
                Something Amazing<br>is Coming
            @endif
        </h1>

        <div class="divider"></div>

        <p class="tagline">
            We are putting the finishing touches on an incredible experience for you. 
            Our team is working hard behind the scenes. We will be live very soon!
        </p>

        <form class="notify-form" onsubmit="event.preventDefault(); this.innerHTML = '<p style=\'color:#4ade80; font-weight:600;\'>✅ You\'re on the list!</p>'">
            <input type="email" class="notify-input" placeholder="Enter your email address">
            <button type="submit" class="notify-btn">Notify Me</button>
        </form>

        <p class="footer-note">
            Are you the site owner? <a href="{{ url('/seller/login') }}" style="color: #4ade80; text-decoration: none;">Log in here</a> to complete your setup.
        </p>
    </div>
</body>
</html>
