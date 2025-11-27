<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 20% 20%, rgba(64, 124, 255, 0.12), transparent 35%),
                        radial-gradient(circle at 80% 0%, rgba(64, 124, 255, 0.18), transparent 40%),
                        #0f172a;
            color: #e2e8f0;
            padding: 2rem 1.5rem;
        }

        .card {
            width: min(960px, 100%);
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.08);
            border-radius: 24px;
            box-shadow: 0 20px 70px rgba(0, 0, 0, 0.45);
            padding: 2.5rem;
            backdrop-filter: blur(12px);
        }

        h1 {
            margin: 0 0 0.5rem;
            font-size: clamp(2rem, 5vw, 2.75rem);
            letter-spacing: -0.03em;
        }

        p {
            margin: 0 0 1.25rem;
            font-size: 1.05rem;
            line-height: 1.7;
            color: #cbd5e1;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .panel {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.06);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: rgba(96, 165, 250, 0.15);
            color: #bfdbfe;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        a {
            color: #93c5fd;
            font-weight: 700;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .cta {
            margin-top: 2.5rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .cta a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 1.15rem;
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.2);
            background: rgba(255, 255, 255, 0.04);
        }

        .cta a.primary {
            background: linear-gradient(135deg, #60a5fa, #4f46e5);
            color: #0f172a;
            border-color: transparent;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4);
        }

        @media (max-width: 640px) {
            .card {
                padding: 1.75rem;
            }

            .cta a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="label">We're here to help</div>
        <h1>Contact AdaSystems</h1>
        <p>
            Have a question about our services, need technical support, or want to start a new project with us?
            Reach out through any of the channels below and our team will get back to you quickly.
        </p>

        <div class="grid">
            <div class="panel">
                <h2>Email</h2>
                <p>Drop us a line at <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a> and we'll respond within one business day.</p>
            </div>
            <div class="panel">
                <h2>Phone</h2>
                <p>Call us at <a href="tel:+441234567890">+44 (0)1234 567 890</a> Monday–Friday, 9:00–18:00 (GMT).</p>
            </div>
            <div class="panel">
                <h2>Visit</h2>
                <p>Come by our studio at 221B Baker Street, London, NW1 6XE. Please schedule an appointment in advance.</p>
            </div>
        </div>

        <div class="cta">
            <a class="primary" href="mailto:hello@adasystems.uk">Send an email</a>
            <a href="tel:+441234567890">Call our team</a>
            <a href="/">Back to home</a>
        </div>
    </main>
</body>
</html>
