@extends('layouts.public')

@section('title', 'ADASystems - IoT Fleet Management & Vehicle Tracking Solutions UK')
@section('description', 'Professional IoT telemetry platform for fleet management. Real-time OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance. Based in Birmingham, UK.')
@section('keywords', 'fleet management UK, IoT telemetry, OBD-II diagnostics, GPS vehicle tracking, tachograph compliance, fleet tracking system, vehicle telematics, commercial fleet management, transport management system, Birmingham')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "ADASystems Fleet Management Platform",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web, Linux",
    "description": "Advanced IoT telemetry platform for fleet management with real-time OBD-II diagnostics, GPS tracking, and EU tachograph compliance.",
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "url": "{{ route('contact.show') }}"
    },
    "provider": {
        "@type": "Organization",
        "name": "ADASystems",
        "url": "{{ url('/') }}"
    }
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "ADASystems - IoT Fleet Management Solutions",
    "description": "Professional IoT telemetry platform for fleet management",
    "url": "{{ url('/') }}",
    "mainEntity": {
        "@type": "Product",
        "name": "ADASystems IoT Telemetry Platform",
        "description": "Complete fleet management solution with OBD-II diagnostics, GPS tracking, and tachograph compliance"
    }
}
</script>
@endsection

@section('styles')
.hero { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 5rem 0; text-align: center; }
.hero h1 { font-size: 3.5rem; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.1; }
.hero p { font-size: 1.25rem; max-width: 700px; margin: 0 auto 2rem; opacity: 0.95; }
.hero-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.button-primary { background: white; color: var(--primary-dark); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-block; }
.button-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
.button-secondary { background: transparent; color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 2px solid white; transition: all 0.3s; }
.button-secondary:hover { background: white; color: var(--primary-dark); }
.features { padding: 5rem 0; background: var(--bg-light); }
.section-title { text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-dark); }
.section-subtitle { text-align: center; color: var(--text-gray); font-size: 1.125rem; max-width: 600px; margin: 0 auto 3rem; }
.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; }
.feature-card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
.feature-icon { font-size: 2.5rem; margin-bottom: 1rem; }
.feature-card h3 { font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--text-dark); }
.feature-card p { color: var(--text-gray); line-height: 1.7; }
.tech-stack { padding: 5rem 0; background: white; }
.tech-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
.tech-item { padding: 1.5rem; border: 1px solid var(--border); border-radius: 8px; transition: all 0.3s; }
.tech-item:hover { border-color: var(--primary); background: var(--bg-light); }
.tech-item h4 { font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--primary-dark); }
.tech-item p { color: var(--text-gray); font-size: 0.95rem; }
.use-cases { padding: 5rem 0; background: var(--bg-light); }
.use-case-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
.use-case { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.use-case h3 { font-size: 1.375rem; margin-bottom: 1.5rem; color: var(--text-dark); }
.use-case ul { list-style: none; }
.use-case li { padding: 0.5rem 0; color: var(--text-gray); position: relative; padding-left: 1.5rem; }
.use-case li::before { content: "✓"; position: absolute; left: 0; color: var(--accent); font-weight: bold; }
.cta-section { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%); color: white; padding: 5rem 0; text-align: center; }
.cta-section h2 { font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; }
.cta-section p { font-size: 1.25rem; opacity: 0.95; margin-bottom: 2rem; }
@media (max-width: 768px) { .hero h1 { font-size: 2.25rem; } .hero p { font-size: 1rem; } .section-title { font-size: 1.75rem; } .cta-section h2 { font-size: 1.75rem; } }
@endsection

@section('content')
<section class="hero">
    <div class="container">
        <h1>Advanced IoT Telemetry<br>for Fleet Management</h1>
        <p>Professional-grade vehicle monitoring combining OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance</p>
        <div class="hero-buttons">
            <a href="#features" class="button-primary">Explore Features</a>
            <a href="{{ route('contact.show') }}" class="button-secondary">Request Demo</a>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <h2 class="section-title">Comprehensive Fleet Telemetry</h2>
        <p class="section-subtitle">Everything you need for professional vehicle monitoring and fleet management in one powerful platform</p>
        <div class="features-grid">
            <article class="feature-card">
                <div class="feature-icon">🚗</div>
                <h3>OBD-II Diagnostics</h3>
                <p>Real-time vehicle diagnostics with support for multiple OBD protocols. Monitor engine health, fuel consumption, speed, RPM, and diagnostic trouble codes (DTCs) in real-time.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon">📍</div>
                <h3>GPS Tracking</h3>
                <p>Accurate GPS positioning with NMEA protocol support. Track vehicle location, routes, speed, and create geofences for comprehensive fleet visibility.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon">📡</div>
                <h3>4G/5G Connectivity</h3>
                <p>Reliable cellular connectivity with automatic APN configuration and network management. Real-time data transmission with offline buffer support.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Tachograph Compliance</h3>
                <p>EU tachograph regulation compliance with driver activity monitoring, working time tracking, and automated compliance reporting.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Power Management</h3>
                <p>Integrated UPS support with battery monitoring. Safe shutdown procedures and power state management for reliable 24/7 operation.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon">💻</div>
                <h3>Modern Interface</h3>
                <p>Touch-optimized interface with real-time dashboards, trip analytics, multi-vehicle support, and comprehensive system monitoring.</p>
            </article>
        </div>
    </div>
</section>

<section class="tech-stack" id="technology">
    <div class="container">
        <h2 class="section-title">Built on Proven Technology</h2>
        <p class="section-subtitle">Modern, modular architecture designed for reliability and scalability</p>
        <div class="tech-grid">
            <div class="tech-item"><h4>Python Backend</h4><p>Modular Python architecture with specialized modules for OBD, GPS, modem control, and fleet management</p></div>
            <div class="tech-item"><h4>Qt/QML Interface</h4><p>Professional touch-optimized user interface with real-time data visualization and multi-tab navigation</p></div>
            <div class="tech-item"><h4>ARM Platform</h4><p>Powerful ARM-based computing platform with excellent connectivity and power efficiency</p></div>
            <div class="tech-item"><h4>SQLite Database</h4><p>Reliable embedded database for local data storage with automatic synchronization</p></div>
            <div class="tech-item"><h4>REST API</h4><p>Modern API architecture for fleet management and cloud integration</p></div>
            <div class="tech-item"><h4>Linux (Ubuntu 24)</h4><p>Stable, secure Linux foundation with long-term support and proven reliability</p></div>
        </div>
    </div>
</section>

<section class="use-cases" id="use-cases">
    <div class="container">
        <h2 class="section-title">Perfect For</h2>
        <p class="section-subtitle">Versatile solutions for various fleet management needs</p>
        <div class="use-case-grid">
            <article class="use-case">
                <h3>🚛 Commercial Fleets</h3>
                <ul>
                    <li>Real-time vehicle health monitoring</li>
                    <li>Route optimization and tracking</li>
                    <li>Fuel consumption analysis</li>
                    <li>Driver behavior monitoring</li>
                    <li>Maintenance scheduling</li>
                </ul>
            </article>
            <article class="use-case">
                <h3>🚖 Transport Services</h3>
                <ul>
                    <li>Tachograph compliance reporting</li>
                    <li>Driver working time tracking</li>
                    <li>Live vehicle location</li>
                    <li>Service quality monitoring</li>
                    <li>Multi-vehicle coordination</li>
                </ul>
            </article>
            <article class="use-case">
                <h3>🏢 Enterprise Solutions</h3>
                <ul>
                    <li>Custom integration capabilities</li>
                    <li>Cloud platform connectivity</li>
                    <li>Advanced analytics and reporting</li>
                    <li>Scalable architecture</li>
                    <li>API-first design</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Transform Your Fleet Management?</h2>
        <p>Get started with ADASystems today and experience professional-grade IoT telemetry</p>
        <a href="{{ route('contact.show') }}" class="button-primary">Contact Us</a>
    </div>
</section>
@endsection
