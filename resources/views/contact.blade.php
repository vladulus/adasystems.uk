<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Primary Meta Tags -->
        <title>Contact Ada Pi Systems - Get IoT Fleet Management Solutions | Request Demo</title>
        <meta name="title" content="Contact Ada Pi Systems - Get IoT Fleet Management Solutions | Request Demo">
        <meta name="description" content="Contact Ada Pi Systems for IoT telemetry and fleet management solutions. Request a demo, get technical support, or inquire about custom fleet tracking systems. Birmingham, UK.">
        <meta name="keywords" content="contact Ada Pi Systems, fleet management inquiry, IoT telemetry demo, vehicle tracking support, OBD-II solutions contact, Birmingham UK">
        <meta name="robots" content="index, follow">
        <meta name="language" content="English">
        <meta name="author" content="Ada Pi Systems">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/contact') }}">
        <meta property="og:title" content="Contact Ada Pi Systems - IoT Fleet Management Solutions">
        <meta property="og:description" content="Get in touch with Ada Pi Systems for professional IoT telemetry and fleet management solutions.">
        <meta property="og:image" content="{{ asset('logo.png') }}">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url('/contact') }}">
        <meta property="twitter:title" content="Contact Ada Pi Systems - IoT Fleet Management Solutions">
        <meta property="twitter:description" content="Get in touch with Ada Pi Systems for professional IoT telemetry and fleet management solutions.">
        <meta property="twitter:image" content="{{ asset('logo.png') }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url('/contact') }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        
        <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
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
                --success: #10b981;
                --error: #ef4444;
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
                background: var(--bg-light);
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

            .contact-hero {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 4rem 0;
                text-align: center;
            }

            .contact-hero h1 {
                font-size: 3rem;
                font-weight: 800;
                margin-bottom: 1rem;
            }

            .contact-hero p {
                font-size: 1.25rem;
                opacity: 0.95;
            }

            .contact-section {
                padding: 4rem 0;
            }

            .contact-container {
                max-width: 800px;
                margin: 0 auto;
                background: var(--bg-white);
                border-radius: 16px;
                padding: 3rem;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: var(--text-dark);
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                width: 100%;
                padding: 0.875rem;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-family: 'Inter', sans-serif;
                font-size: 1rem;
                transition: all 0.3s;
            }

            .form-group input:focus,
            .form-group textarea:focus,
            .form-group select:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }

            .form-group textarea {
                min-height: 150px;
                resize: vertical;
            }

            .required {
                color: var(--error);
            }

            .submit-button {
                background: var(--primary);
                color: white;
                padding: 1rem 2.5rem;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                width: 100%;
            }

            .submit-button:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            }

            .submit-button:disabled {
                background: var(--text-gray);
                cursor: not-allowed;
                transform: none;
            }

            .alert {
                padding: 1rem;
                border-radius: 8px;
                margin-bottom: 1.5rem;
                font-weight: 500;
            }

            .alert-success {
                background: #d1fae5;
                color: #065f46;
                border: 1px solid #10b981;
            }

            .alert-error {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #ef4444;
            }

            .contact-info {
                margin-top: 3rem;
                padding-top: 3rem;
                border-top: 1px solid var(--border);
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
                margin-top: 2rem;
            }

            .info-item {
                text-align: center;
            }

            .info-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, var(--primary), var(--accent));
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                font-size: 1.5rem;
            }

            .info-item h3 {
                font-size: 1.125rem;
                margin-bottom: 0.5rem;
                color: var(--text-dark);
            }

            .info-item p {
                color: var(--text-gray);
            }

            .info-item a {
                color: var(--primary);
                text-decoration: none;
            }

            .info-item a:hover {
                text-decoration: underline;
            }

            footer {
                background: var(--text-dark);
                color: white;
                padding: 2rem 0;
                margin-top: 4rem;
            }

            .footer-content {
                text-align: center;
                color: rgba(255,255,255,0.7);
            }

            .footer-content a {
                color: white;
                text-decoration: none;
            }

            @media (max-width: 768px) {
                .contact-hero h1 {
                    font-size: 2rem;
                }

                .contact-container {
                    padding: 2rem 1.5rem;
                }

                .nav-links {
                    display: none;
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
                        <li><a href="{{ url('/') }}#features">Features</a></li>
                        <li><a href="{{ url('/') }}#technology">Technology</a></li>
                        <li><a href="{{ url('/') }}#use-cases">Use Cases</a></li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <section class="contact-hero">
            <div class="container">
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </section>

        <section class="contact-section">
            <div class="container">
                <div class="contact-container">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul style="margin: 0; padding-left: 1.5rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                        </div>

                        <div class="form-group">
                            <label for="company">Company Name</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                <option value="Product Demo" {{ old('subject') == 'Product Demo' ? 'selected' : '' }}>Product Demo Request</option>
                                <option value="Technical Support" {{ old('subject') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                                <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership Opportunity</option>
                                <option value="Custom Solution" {{ old('subject') == 'Custom Solution' ? 'selected' : '' }}>Custom Solution Inquiry</option>
                                <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="submit-button">Send Message</button>
                    </form>

                    <div class="contact-info">
                        <h2 style="text-align: center; margin-bottom: 1rem;">Other Ways to Reach Us</h2>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-icon">📧</div>
                                <h3>Email</h3>
                                <p><a href="mailto:hello@adasystems.uk">hello@adasystems.uk</a></p>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">📍</div>
                                <h3>Location</h3>
                                <p>Birmingham<br>United Kingdom</p>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">⏰</div>
                                <h3>Business Hours</h3>
                                <p>Monday - Friday<br>9:00 AM - 5:00 PM GMT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="container">
                <div class="footer-content">
                    <p>&copy; {{ date('Y') }} Ada Pi Systems. All rights reserved. | <a href="{{ url('/') }}">Back to Home</a></p>
                </div>
            </div>
        </footer>
    </body>
</html>
