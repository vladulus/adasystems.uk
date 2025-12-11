<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'ADASystems - IoT Fleet Management Solutions')</title>
    <meta name="title" content="@yield('title', 'ADASystems - IoT Fleet Management Solutions')">
    <meta name="description" content="@yield('description', 'Professional IoT telemetry platform for fleet management. Real-time OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance.')">
    <meta name="keywords" content="@yield('keywords', 'IoT telemetry, fleet management, OBD-II diagnostics, GPS tracking, vehicle monitoring, tachograph compliance')">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="author" content="ADASystems">
    <meta name="revisit-after" content="7 days">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'ADASystems - IoT Fleet Management Solutions')">
    <meta property="og:description" content="@yield('description', 'Professional IoT telemetry platform for fleet management.')">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:site_name" content="ADASystems">
    <meta property="og:locale" content="en_GB">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'ADASystems - IoT Fleet Management Solutions')">
    <meta name="twitter:description" content="@yield('description', 'Professional IoT telemetry platform for fleet management.')">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    
    <!-- Geo Tags -->
    <meta name="geo.region" content="GB-BIR">
    <meta name="geo.placename" content="Birmingham">
    <meta name="geo.position" content="52.4862;-1.8904">
    <meta name="ICBM" content="52.4862, -1.8904">
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "ADASystems",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('logo.png') }}",
        "description": "Professional IoT telemetry solutions for fleet management.",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Birmingham",
            "addressCountry": "GB"
        },
        "email": "hello@adasystems.uk",
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "hello@adasystems.uk",
            "contactType": "Customer Service",
            "availableLanguage": "English"
        }
    }
    </script>
    @yield('schema')
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --accent: #10b981;
            --success: #10b981;
            --error: #ef4444;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --border: #e5e7eb;
        }
        body { font-family: 'Inter', sans-serif; color: var(--text-dark); line-height: 1.6; background: var(--bg-white); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: var(--bg-white); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; }
        .logo { height: 50px; width: auto; }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-gray); font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: var(--primary); }
        .cta-button { background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .cta-button:hover { background: var(--primary-dark); transform: translateY(-2px); }
        footer { background: var(--text-dark); color: white; padding: 3rem 0 1.5rem; }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; }
        .footer-section h4 { font-size: 1rem; margin-bottom: 1rem; color: white; }
        .footer-section a { display: block; color: #9ca3af; text-decoration: none; margin-bottom: 0.5rem; transition: color 0.3s; }
        .footer-section a:hover { color: white; }
        .footer-section p { color: #9ca3af; font-size: 0.9rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid #374151; color: #9ca3af; font-size: 0.85rem; }
        @media (max-width: 768px) { .nav-links { display: none; } .cta-button { padding: 0.5rem 1rem; font-size: 0.9rem; } }
        @yield('styles')
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="{{ route('home') }}" aria-label="ADASystems Home">
                    <img src="{{ asset('logo.png') }}" alt="ADASystems - Fleet Management Solutions" class="logo">
                </a>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}#features">Features</a></li>
                    <li><a href="{{ route('home') }}#technology">Technology</a></li>
                    <li><a href="{{ route('home') }}#use-cases">Use Cases</a></li>
                    <li><a href="{{ route('app.download') }}">App</a></li>
                    <li><a href="{{ route('contact.show') }}">Contact</a></li>
                </ul>
                <a href="{{ route('login') }}" class="cta-button">Login</a>
            </nav>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>ADASystems</h4>
                    <p>Advanced IoT telemetry solutions for professional fleet management.</p>
                </div>
                <div class="footer-section">
                    <h4>Legal</h4>
                    <a href="{{ route('legal.terms') }}">Terms & Conditions</a>
                    <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
                    <a href="{{ route('legal.cookies') }}">Cookie Policy</a>
                    <a href="{{ route('legal.aup') }}">Acceptable Use</a>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a>
                    <p>Birmingham, United Kingdom</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} ADASystems. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
