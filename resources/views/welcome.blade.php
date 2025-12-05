<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Primary Meta Tags -->
        <title>Ada Pi Systems - IoT Telemetry Solutions for Fleet Management | OBD-II GPS Tracking</title>
        <meta name="title" content="Ada Pi Systems - IoT Telemetry Solutions for Fleet Management | OBD-II GPS Tracking">
        <meta name="description" content="Professional IoT telemetry platform for fleet management. Real-time OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance. Perfect for commercial fleets and transport services.">
        <meta name="keywords" content="IoT telemetry, fleet management, OBD-II diagnostics, GPS tracking, vehicle monitoring, Raspberry Pi, tachograph compliance, fleet tracking system, vehicle telemetry, automotive diagnostics, 4G 5G connectivity">
        <meta name="robots" content="index, follow">
        <meta name="language" content="English">
        <meta name="author" content="Ada Pi Systems">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="Ada Pi Systems - IoT Telemetry Solutions for Fleet Management">
        <meta property="og:description" content="Professional IoT telemetry platform combining OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance for fleet management.">
        <meta property="og:image" content="{{ asset('logo.png') }}">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url('/') }}">
        <meta property="twitter:title" content="Ada Pi Systems - IoT Telemetry Solutions for Fleet Management">
        <meta property="twitter:description" content="Professional IoT telemetry platform combining OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance for fleet management.">
        <meta property="twitter:image" content="{{ asset('logo.png') }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url('/') }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        
        <!-- Geo Tags -->
        <meta name="geo.region" content="GB-BIR">
        <meta name="geo.placename" content="Birmingham">
        <meta name="geo.position" content="52.4862;-1.8904">
        <meta name="ICBM" content="52.4862, -1.8904">
        
        <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Structured Data / Schema.org -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Ada Pi Systems",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('logo.png') }}",
            "description": "Professional IoT telemetry solutions for fleet management combining OBD-II diagnostics, GPS tracking, and 4G/5G connectivity.",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Birmingham",
                "addressCountry": "GB"
            },
            "email": "hello@adasystems.uk",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+44",
                "contactType": "Customer Service",
                "email": "hello@adasystems.uk",
                "availableLanguage": "English"
            },
            "sameAs": []
        }
        </script>
        
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Product",
            "name": "Ada Pi Systems IoT Telemetry Platform",
            "description": "Advanced IoT telemetry platform for fleet management with OBD-II diagnostics, GPS tracking, 4G/5G connectivity, and EU tachograph compliance.",
            "brand": {
                "@type": "Brand",
                "name": "Ada Pi Systems"
            },
            "offers": {
                "@type": "Offer",
                "availability": "https://schema.org/InStock",
                "url": "{{ url('/contact') }}"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "5",
                "reviewCount": "1"
            }
        }
        </script>
        
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Ada Pi Systems",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Linux",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "GBP"
            },
            "description": "IoT telemetry platform for professional fleet management and vehicle monitoring."
        }
        </script>
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --primary: #2563eb;
                --primary-dark: #1e40af;
                --accent: #10b981;
                --text-dark: #1f2937;
                --text-gray: #6b7280;
                --bg-light: #f9fafb;
                --bg-white: #ffffff;
                --border: #e5e7eb;
            }

            body {
                font-family: 'Inter', sans-serif;
                color: var(--text-dark);
                line-height: 1.6;
                background: var(--bg-white);
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }

            header {
                background: var(--bg-white);
                border-bottom: 1px solid var(--border);
                position: sticky;
                top: 0;
                z-index: 100;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1rem 0;
            }

            .logo {
                height: 50px;
                width: auto;
            }

            .nav-links {
                display: flex;
                gap: 2rem;
                list-style: none;
            }

            .nav-links a {
                text-decoration: none;
                color: var(--text-gray);
                font-weight: 500;
                transition: color 0.3s;
            }

            .nav-links a:hover {
                color: var(--primary);
            }

            .cta-button {
                background: var(--primary);
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
                border: none;
                cursor: pointer;
            }

            .cta-button:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            }

            .hero {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 6rem 0;
                text-align: center;
            }

            .hero h1 {
                font-size: 3.5rem;
                font-weight: 800;
                margin-bottom: 1.5rem;
                line-height: 1.2;
            }

            .hero p {
                font-size: 1.25rem;
                max-width: 700px;
                margin: 0 auto 2rem;
                opacity: 0.95;
            }

            .hero-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .button-primary {
                background: white;
                color: var(--primary);
                padding: 1rem 2rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
                display: inline-block;
            }

            .button-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            }

            .button-secondary {
                background: transparent;
                color: white;
                padding: 1rem 2rem;
                border: 2px solid white;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
                display: inline-block;
            }

            .button-secondary:hover {
                background: white;
                color: var(--primary);
            }

            .features {
                padding: 5rem 0;
                background: var(--bg-white);
            }

            .section-title {
                text-align: center;
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: var(--text-dark);
            }

            .section-subtitle {
                text-align: center;
                font-size: 1.125rem;
                color: var(--text-gray);
                max-width: 600px;
                margin: 0 auto 3rem;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 3rem;
            }

            .feature-card {
                background: var(--bg-white);
                padding: 2rem;
                border-radius: 12px;
                border: 1px solid var(--border);
                transition: all 0.3s;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                border-color: var(--primary);
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, var(--primary), var(--accent));
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
                font-size: 2rem;
            }

            .feature-card h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: var(--text-dark);
            }

            .feature-card p {
                color: var(--text-gray);
                line-height: 1.7;
            }

            .tech-stack {
                padding: 5rem 0;
                background: var(--bg-light);
            }

            .tech-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
                margin-top: 3rem;
            }

            .tech-item {
                background: var(--bg-white);
                padding: 1.5rem;
                border-radius: 8px;
                text-align: center;
                border: 1px solid var(--border);
            }

            .tech-item h4 {
                font-size: 1.125rem;
                margin-bottom: 0.5rem;
                color: var(--text-dark);
            }

            .tech-item p {
                color: var(--text-gray);
                font-size: 0.875rem;
            }

            .use-cases {
                padding: 5rem 0;
                background: var(--bg-white);
            }

            .use-case-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 2rem;
                margin-top: 3rem;
            }

            .use-case {
                background: var(--bg-light);
                padding: 2rem;
                border-radius: 12px;
                border-left: 4px solid var(--primary);
            }

            .use-case h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: var(--text-dark);
            }

            .use-case ul {
                list-style: none;
                margin-top: 1rem;
            }

            .use-case li {
                padding: 0.5rem 0;
                color: var(--text-gray);
                padding-left: 1.5rem;
                position: relative;
            }

            .use-case li:before {
                content: "✓";
                position: absolute;
                left: 0;
                color: var(--accent);
                font-weight: bold;
            }

            .cta-section {
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                color: white;
                padding: 5rem 0;
                text-align: center;
            }

            .cta-section h2 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .cta-section p {
                font-size: 1.25rem;
                margin-bottom: 2rem;
                opacity: 0.9;
            }

            footer {
                background: var(--text-dark);
                color: white;
                padding: 3rem 0 1rem;
            }

            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .footer-section h4 {
                margin-bottom: 1rem;
                font-size: 1.125rem;
            }

            .footer-section p,
            .footer-section a {
                color: rgba(255,255,255,0.7);
                text-decoration: none;
                display: block;
                margin-bottom: 0.5rem;
            }

            .footer-section a:hover {
                color: white;
            }

            .footer-bottom {
                border-top: 1px solid rgba(255,255,255,0.1);
                padding-top: 2rem;
                text-align: center;
                color: rgba(255,255,255,0.5);
            }

            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 2.5rem;
                }

                .nav-links {
                    display: none;
                }

                .features-grid,
                .tech-grid,
                .use-case-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container">
                <nav>
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('logo.png') }}" alt="Ada Pi Systems" class="logo">
                    </a>
                    <ul class="nav-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#technology">Technology</a></li>
                        <li><a href="#use-cases">Use Cases</a></li>
                        <li><a href="{{ route('contact.show') }}">Contact</a></li>
						<li><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                    <a href="{{ route('contact.show') }}" class="cta-button">Get Started</a>
                </nav>
            </div>
        </header>

        <section class="hero" itemscope itemtype="https://schema.org/WebPageElement">
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
                    <div class="feature-card">
                        <div class="feature-icon">🚗</div>
                        <h3>OBD-II Diagnostics</h3>
                        <p>Real-time vehicle diagnostics with support for multiple OBD protocols. Monitor engine health, fuel consumption, speed, RPM, and diagnostic trouble codes (DTCs) in real-time.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📍</div>
                        <h3>GPS Tracking</h3>
                        <p>Accurate GPS positioning with NMEA protocol support. Track vehicle location, routes, speed, and create geofences for comprehensive fleet visibility.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📡</div>
                        <h3>4G/5G Connectivity</h3>
                        <p>Reliable cellular connectivity with automatic APN configuration and network management. Real-time data transmission with offline buffer support.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3>Tachograph Compliance</h3>
                        <p>EU tachograph regulation compliance with driver activity monitoring, working time tracking, and automated compliance reporting.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3>Power Management</h3>
                        <p>Integrated UPS support with X1202 battery monitoring. Safe shutdown procedures and power state management for reliable 24/7 operation.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">💻</div>
                        <h3>Modern Interface</h3>
                        <p>Touch-optimized QML interface with real-time dashboards, trip analytics, multi-vehicle support, and comprehensive system monitoring.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="tech-stack" id="technology">
            <div class="container">
                <h2 class="section-title">Built on Proven Technology</h2>
                <p class="section-subtitle">Modern, modular architecture designed for reliability and scalability</p>
                
                <div class="tech-grid">
                    <div class="tech-item">
                        <h4>Python Backend</h4>
                        <p>Modular Python architecture with specialized modules for OBD, GPS, modem control, and fleet management</p>
                    </div>

                    <div class="tech-item">
                        <h4>Qt/QML Interface</h4>
                        <p>Professional touch-optimized user interface with real-time data visualization and multi-tab navigation</p>
                    </div>

                    <div class="tech-item">
                        <h4>Raspberry Pi 5</h4>
                        <p>Powerful ARM-based computing platform with excellent connectivity and power efficiency</p>
                    </div>

                    <div class="tech-item">
                        <h4>SQLite Database</h4>
                        <p>Reliable embedded database for local data storage with automatic synchronization</p>
                    </div>

                    <div class="tech-item">
                        <h4>REST API</h4>
                        <p>Modern API architecture for fleet management and cloud integration</p>
                    </div>

                    <div class="tech-item">
                        <h4>Linux (Ubuntu 24)</h4>
                        <p>Stable, secure Linux foundation with long-term support and proven reliability</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="use-cases" id="use-cases">
            <div class="container">
                <h2 class="section-title">Perfect For</h2>
                <p class="section-subtitle">Versatile solutions for various fleet management needs</p>
                
                <div class="use-case-grid">
                    <div class="use-case">
                        <h3>🚛 Commercial Fleets</h3>
                        <ul>
                            <li>Real-time vehicle health monitoring</li>
                            <li>Route optimization and tracking</li>
                            <li>Fuel consumption analysis</li>
                            <li>Driver behavior monitoring</li>
                            <li>Maintenance scheduling</li>
                        </ul>
                    </div>

                    <div class="use-case">
                        <h3>🚖 Transport Services</h3>
                        <ul>
                            <li>Tachograph compliance reporting</li>
                            <li>Driver working time tracking</li>
                            <li>Live vehicle location</li>
                            <li>Service quality monitoring</li>
                            <li>Multi-vehicle coordination</li>
                        </ul>
                    </div>

                    <div class="use-case">
                        <h3>🏢 Enterprise Solutions</h3>
                        <ul>
                            <li>Custom integration capabilities</li>
                            <li>Cloud platform connectivity</li>
                            <li>Advanced analytics and reporting</li>
                            <li>Scalable architecture</li>
                            <li>API-first design</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section" id="contact">
            <div class="container">
                <h2>Ready to Transform Your Fleet Management?</h2>
                <p>Get started with Ada Pi Systems today and experience professional-grade IoT telemetry</p>
                <a href="{{ route('contact.show') }}" class="button-primary">Contact Us</a>
            </div>
        </section>

        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h4>Ada Pi Systems</h4>
                        <p>Advanced IoT telemetry solutions for professional fleet management.</p>
                    </div>

                    <div class="footer-section">
                        <h4>Product</h4>
                        <a href="#features">Features</a>
                        <a href="#technology">Technology</a>
                        <a href="#use-cases">Use Cases</a>
                    </div>

                    <div class="footer-section">
                        <h4>Resources</h4>
                        <a href="#">Documentation</a>
                        <a href="#">API Reference</a>
                        <a href="#">Support</a>
                    </div>

                    <div class="footer-section">
                        <h4>Contact</h4>
                        <a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a>
                        <p>Birmingham, United Kingdom</p>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} Ada Pi Systems. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
