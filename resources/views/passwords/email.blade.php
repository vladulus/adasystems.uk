<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Forgot Password - Ada Pi Systems</title>
        <meta name="description" content="Reset your Ada Pi Systems account password.">
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

            .container {
                background: var(--bg-white);
                border-radius: 18px;
                border: 1px solid rgba(148, 163, 184, 0.35);
                box-shadow: 0 18px 45px rgba(79, 70, 229, 0.12), 0 0 0 1px rgba(148, 163, 184, 0.15);
                width: 100%;
                max-width: 450px;
                overflow: hidden;
            }

            .header {
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

            .header h1 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }

            .header p {
                opacity: 0.9;
                font-size: 0.9rem;
            }

            .body {
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

            .alert-info {
                background: rgba(59, 130, 246, 0.1);
                color: #1e40af;
                border: 1px solid #3b82f6;
            }

            .info-box {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
                color: var(--text-gray);
            }

            .info-box i {
                color: var(--primary);
                margin-right: 0.5rem;
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

            .back-link {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                color: var(--text-gray);
                text-decoration: none;
                font-weight: 500;
                font-size: 0.95rem;
                transition: color 0.3s;
            }

            .back-link:hover {
                color: var(--primary);
            }

            .footer {
                background: var(--bg-light);
                padding: 1.25rem 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: var(--text-gray);
            }

            @media (max-width: 480px) {
                .header {
                    padding: 1.5rem;
                }

                .header h1 {
                    font-size: 1.25rem;
                }

                .body {
                    padding: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Ada Pi Systems" class="logo">
                <h1>Forgot Password</h1>
                <p>Reset your account password</p>
            </div>

            <div class="body">
                @if(session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
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

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    Enter your email address and we'll send you instructions to reset your password.
                </div>

                <form action="{{ route('password.email') }}" method="POST">
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

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Link
                    </button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Login
                </a>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Ada Pi Systems. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
