@extends('layouts.app-mobile')

@section('content')
<div class="app-home">
    {{-- Login Section (shown when not authenticated) --}}
    @guest
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('logo.png') }}" alt="ADA Systems" class="login-logo">
            <h1>ADA Systems</h1>
            <p>Fleet Management</p>
        </div>

        <form method="POST" action="{{ route('login.authenticate') }}" class="login-form">
            @csrf
            <input type="hidden" name="redirect_to" value="app.home">
            
            @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <label class="remember-me">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            <button type="submit" class="btn-login">
                <span>Sign In</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
    </div>
    @endguest

    {{-- Dashboard Section (shown when authenticated) --}}
    @auth
    <div class="dashboard-container">
        {{-- Header --}}
        <div class="dashboard-header">
            <div class="user-greeting">
                <span class="greeting-text">Hello,</span>
                <span class="user-name">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>

        {{-- Quick Stats --}}
        <div class="quick-stats">
            @php
                $user = auth()->user();
                $devices = $user->getVisibleDevices();
                $onlineCount = $devices->filter(fn($d) => $d->isOnline())->count();
            @endphp
            <div class="stat-card">
                <div class="stat-icon online">
                    <i class="fas fa-signal"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $onlineCount }}</span>
                    <span class="stat-label">Online</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $devices->count() }}</span>
                    <span class="stat-label">Devices</span>
                </div>
            </div>
        </div>

        {{-- Navigation Grid --}}
        <div class="nav-grid">
            @can('dashboard.access')
            <a href="{{ route('app.pi') }}" class="nav-card">
                <div class="nav-icon pi">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <span class="nav-label">Pi Dashboard</span>
                <span class="nav-desc">Live device data</span>
            </a>
            @endcan

            @can('devices.view')
            <a href="{{ route('management.devices.index') }}" class="nav-card">
                <div class="nav-icon devices">
                    <i class="fas fa-microchip"></i>
                </div>
                <span class="nav-label">Devices</span>
                <span class="nav-desc">Manage devices</span>
            </a>
            @endcan

            @can('vehicles.view')
            <a href="{{ route('management.vehicles.index') }}" class="nav-card">
                <div class="nav-icon vehicles">
                    <i class="fas fa-truck"></i>
                </div>
                <span class="nav-label">Vehicles</span>
                <span class="nav-desc">Fleet overview</span>
            </a>
            @endcan

            @can('users.view')
            <a href="{{ route('management.users.index') }}" class="nav-card">
                <div class="nav-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <span class="nav-label">Users</span>
                <span class="nav-desc">User accounts</span>
            </a>
            @endcan

            @can('drivers.view')
            <a href="{{ route('management.drivers.index') }}" class="nav-card">
                <div class="nav-icon drivers">
                    <i class="fas fa-id-card"></i>
                </div>
                <span class="nav-label">Drivers</span>
                <span class="nav-desc">Driver profiles</span>
            </a>
            @endcan
        </div>

        {{-- Footer --}}
        <div class="app-footer">
            <span>ADA Systems v1.0</span>
        </div>
    </div>
    @endauth
</div>
@endsection