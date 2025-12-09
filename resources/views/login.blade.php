<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - ADASystems</title>
        <meta name="description" content="Login to ADASystems IoT telemetry dashboard for fleet management and vehicle monitoring.">
        <meta name="robots" content="noindex, nofollow">
        <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --primary: #4f46e5;
                --primary-dark: #4338ca;
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
                background: #f3f4f6;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .login-container {
                background: var(--bg-white);
                border-radius: 18px;
                border: 1px solid rgba(148, 163, 184, 0.35);
                box-shadow: 0 18px 45px rgba(79, 70, 229, 0.12), 0 0 0 1px rgba(148, 163, 184, 0.15);
                width: 100%;
                max-width: 450px;
                overflow: hidden;
            }

            .login-header {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                padding: 2rem;
                text-align: center;
                color: white;
            }

            .logo {
                height: 50px;
                width: auto;
                margin-bottom: 1rem;
                filter: brightness(0) invert(1);
            }

            .login-header h1 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }

            .login-header p {
                opacity: 0.9;
                font-size: 0.9rem;
            }

            .login-body {
                padding: 2rem;
            }

            .alert {
                padding: 1rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
                font-weight: 500;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .alert-success {
                background: rgba(16, 185, 129, 0.1);
                color: #065f46;
                border: 1px solid #10b981;
            }

            .alert-error {
                background: rgba(239, 68, 68, 0.1);
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
                border-radius: 12px;
                font-family: 'Inter', sans-serif;
                font-size: 1rem;
                transition: all 0.3s;
            }

            .form-group input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
                font-size: 1rem;
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
                accent-color: var(--primary);
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

            .btn {
                width: 100%;
                padding: 1rem;
                border: none;
                border-radius: 12px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                font-family: 'Inter', sans-serif;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .btn-primary {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                color: white;
                box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 14px 30px rgba(79, 70, 229, 0.45);
            }

            .btn-primary:disabled {
                background: var(--text-gray);
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            .divider {
                display: flex;
                align-items: center;
                margin: 1.5rem 0;
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
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                color: var(--text-gray);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
                font-size: 0.95rem;
            }

            .back-home a:hover {
                color: var(--primary);
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
                font-weight: 600;
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

            .login-footer {
                background: var(--bg-light);
                padding: 1.25rem 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: var(--text-gray);
            }

            @media (max-width: 480px) {
                .login-header {
                    padding: 1.5rem;
                }

                .login-header h1 {
                    font-size: 1.25rem;
                }

                .login-body {
                    padding: 1.5rem;
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
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
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
                                <i class="fas fa-eye" id="toggleIcon"></i>
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

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Login to Dashboard
                    </button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="back-home">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-arrow-left"></i>
                        Back to Homepage
                    </a>
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
                const toggleIcon = document.getElementById('toggleIcon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
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
