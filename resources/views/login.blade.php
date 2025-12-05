<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - Ada Pi Systems Dashboard</title>
        <meta name="description" content="Login to Ada Pi Systems IoT telemetry dashboard for fleet management and vehicle monitoring.">
        <meta name="robots" content="noindex, nofollow">
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
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .login-container {
                background: var(--bg-white);
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 450px;
                overflow: hidden;
            }

            .login-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 2.5rem 2rem;
                text-align: center;
                color: white;
            }

            .logo {
                height: 60px;
                width: auto;
                margin-bottom: 1rem;
                filter: brightness(0) invert(1);
            }

            .login-header h1 {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }

            .login-header p {
                opacity: 0.9;
                font-size: 0.95rem;
            }

            .login-body {
                padding: 2.5rem 2rem;
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

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: var(--text-dark);
                font-size: 0.95rem;
            }

            .form-group input {
                width: 100%;
                padding: 0.875rem 1rem;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-family: 'Inter', sans-serif;
                font-size: 1rem;
                transition: all 0.3s;
            }

            .form-group input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }

            .form-group input::placeholder {
                color: #9ca3af;
            }

            .password-wrapper {
                position: relative;
            }

            .toggle-password {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: var(--text-gray);
                cursor: pointer;
                font-size: 1.25rem;
                padding: 0.25rem;
                transition: color 0.3s;
            }

            .toggle-password:hover {
                color: var(--primary);
            }

            .form-options {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
            }

            .remember-me {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .remember-me input[type="checkbox"] {
                width: auto;
                cursor: pointer;
            }

            .remember-me label {
                margin: 0;
                font-weight: 500;
                color: var(--text-gray);
                cursor: pointer;
            }

            .forgot-password {
                color: var(--primary);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
            }

            .forgot-password:hover {
                color: var(--primary-dark);
                text-decoration: underline;
            }

            .login-button {
                width: 100%;
                background: var(--primary);
                color: white;
                padding: 1rem;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                font-family: 'Inter', sans-serif;
            }

            .login-button:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            }

            .login-button:disabled {
                background: var(--text-gray);
                cursor: not-allowed;
                transform: none;
            }

            .divider {
                display: flex;
                align-items: center;
                margin: 2rem 0;
                color: var(--text-gray);
                font-size: 0.9rem;
            }

            .divider::before,
            .divider::after {
                content: '';
                flex: 1;
                border-top: 1px solid var(--border);
            }

            .divider span {
                padding: 0 1rem;
            }

            .back-home {
                text-align: center;
            }

            .back-home a {
                color: var(--text-gray);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
                font-size: 0.95rem;
            }

            .back-home a:hover {
                color: var(--primary);
            }

            .login-footer {
                background: var(--bg-light);
                padding: 1.5rem 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: var(--text-gray);
            }

            .features-list {
                margin-top: 1.5rem;
                padding-top: 1.5rem;
                border-top: 1px solid var(--border);
            }

            .features-list h3 {
                font-size: 0.9rem;
                margin-bottom: 1rem;
                color: var(--text-dark);
                text-align: center;
            }

            .features-list ul {
                list-style: none;
                font-size: 0.85rem;
                color: var(--text-gray);
            }

            .features-list li {
                padding: 0.5rem 0;
                padding-left: 1.5rem;
                position: relative;
            }

            .features-list li:before {
                content: "✓";
                position: absolute;
                left: 0;
                color: var(--accent);
                font-weight: bold;
            }

            @media (max-width: 480px) {
                .login-header {
                    padding: 2rem 1.5rem;
                }

                .login-header h1 {
                    font-size: 1.5rem;
                }

                .login-body {
                    padding: 2rem 1.5rem;
                }

                .form-options {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: flex-start;
                }
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <img src="{{ asset('logo.png') }}" alt="Ada Pi Systems" class="logo">
                <h1>Dashboard Login</h1>
                <p>Access your IoT telemetry dashboard</p>
            </div>

            <div class="login-body">
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
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="your@email.com"
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-button">Login to Dashboard</button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="back-home">
                    <a href="{{ url('/') }}">← Back to Homepage</a>
                </div>

                <div class="features-list">
                    <h3>Dashboard Features</h3>
                    <ul>
                        <li>Real-time vehicle monitoring</li>
                        <li>OBD-II diagnostics</li>
                        <li>GPS tracking & route history</li>
                        <li>Fleet analytics</li>
                        <li>Tachograph compliance</li>
                    </ul>
                </div>
            </div>

            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Ada Pi Systems. All rights reserved.</p>
            </div>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleButton = document.querySelector('.toggle-password');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleButton.textContent = '👁️‍🗨️';
                } else {
                    passwordInput.type = 'password';
                    toggleButton.textContent = '👁️';
                }
            }

            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.3s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }, 5000);
                });
            });
        </script>
    </body>
</html>
