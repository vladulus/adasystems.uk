<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#7c3aed">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <title>ADA Systems</title>
    
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.bunny.net/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #a78bfa;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --text-light: #94a3b8;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --border: #e2e8f0;
            --safe-top: env(safe-area-inset-top);
            --safe-bottom: env(safe-area-inset-bottom);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            min-height: 100vh;
            color: var(--text-dark);
            padding-top: var(--safe-top);
            padding-bottom: var(--safe-bottom);
        }

        /* ==========================================
           APP HOME STYLES
           ========================================== */
        .app-home {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* LOGIN CONTAINER */
        .login-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 16px;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-gray);
        }

        .login-form {
            width: 100%;
            max-width: 340px;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 16px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 16px;
            font-family: inherit;
            background: var(--bg-white);
            transition: all 0.2s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-gray);
        }

        .remember-me input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        /* DASHBOARD CONTAINER */
        .dashboard-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            padding-top: calc(20px + var(--safe-top));
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .user-greeting {
            display: flex;
            flex-direction: column;
        }

        .greeting-text {
            font-size: 14px;
            color: var(--text-gray);
        }

        .user-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .logout-form {
            margin: 0;
        }

        .btn-logout {
            width: 44px;
            height: 44px;
            border: none;
            background: var(--bg-white);
            border-radius: 12px;
            color: var(--text-gray);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
        }

        .btn-logout:active {
            background: #f1f5f9;
        }

        /* QUICK STATS */
        .quick-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            flex: 1;
            background: var(--bg-white);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.online {
            background: #d1fae5;
            color: #059669;
        }

        .stat-icon.total {
            background: #ede9fe;
            color: var(--primary);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        /* NAVIGATION GRID */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            flex: 1;
        }

        .nav-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 24px 20px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .nav-card:active {
            transform: scale(0.97);
            border-color: var(--primary-light);
        }

        .nav-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .nav-icon.pi {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #7c3aed;
        }

        .nav-icon.devices {
            background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
            color: #2563eb;
        }

        .nav-icon.vehicles {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #059669;
        }

        .nav-icon.users {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
            color: #db2777;
        }

        .nav-icon.drivers {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #ea580c;
        }

        .nav-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .nav-desc {
            font-size: 12px;
            color: var(--text-gray);
        }

        /* FOOTER */
        .app-footer {
            text-align: center;
            padding: 20px;
            color: var(--text-light);
            font-size: 12px;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
