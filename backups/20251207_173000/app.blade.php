<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Ada Pi Systems</title>

    <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f3f4f6; /* fundal deschis, fără gradient */
            min-height: 100vh;
            color: #111827;
        }

        /* Header */
        .header {
            background: #ffffff;
            backdrop-filter: blur(8px);
            padding: 1rem 2rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            border-bottom: 1px solid rgba(148, 163, 184, 0.4);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            height: 40px;
            width: auto;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .welcome h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }

        .welcome .role {
            font-size: 0.75rem;
            font-weight: 600;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.08);
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            display: inline-block;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-email {
            color: #64748b;
            font-size: 0.9rem;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.35);
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Flash Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #065f46;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #991b1b;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid #f59e0b;
            color: #92400e;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-inner {
                flex-direction: column;
                gap: 0.75rem;
                text-align: center;
            }

            .header {
                padding: 0.75rem 1rem;
            }

            .header-right {
                flex-direction: column;
                gap: 0.75rem;
            }

            .main-content {
                padding: 1.25rem;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-inner">
            <div class="header-left">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('logo.png') }}" alt="Ada Pi Systems" class="logo">
                </a>
                @php
                   $primaryRole = auth()->user()
                       ? auth()->user()->getRoleNames()->first()
                       : null;
                @endphp

                <div class="welcome">
                    <h1>Welcome, {{ auth()->user()->name ?? 'User' }}!</h1>
                    <span class="role">{{ $primaryRole ? ucfirst($primaryRole) : 'User' }}</span>
                </div>
            </div>

            <div class="header-right">
                <span class="user-email">{{ auth()->user()->email ?? '' }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        {{-- Flash Messages --}}
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

        @if(session('warning'))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('warning') }}
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
