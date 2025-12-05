@extends('layouts.app')

@section('title', 'Management Dashboard')

@section('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
    }

    .stat-icon.devices { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-icon.vehicles { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.users { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.drivers { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

    .stat-title {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }

    .stat-details {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-detail {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
    }

    .stat-detail .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.active { background: #10b981; }
    .dot.inactive { background: #94a3b8; }
    .dot.maintenance { background: #f59e0b; }
    .dot.admin { background: #667eea; }
    .dot.leave { background: #ef4444; }

    .progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .progress-fill.devices { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
    .progress-fill.vehicles { background: linear-gradient(90deg, #10b981 0%, #059669 100%); }
    .progress-fill.users { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }
    .progress-fill.drivers { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); }

    .manage-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .manage-link:hover {
        color: #764ba2;
        gap: 0.75rem;
    }

    /* Search */
    .search-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .search-wrapper {
        display: flex;
        gap: 1rem;
    }

    .search-input-wrapper {
        flex: 1;
        position: relative;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .search-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    }

    .search-hint {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 0.75rem;
    }

    /* Quick Actions */
    .quick-actions {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .quick-actions h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quick-actions h3 i {
        color: #667eea;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #1a1a2e;
    }

    .action-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        transform: translateY(-2px);
    }

    .action-btn i {
        font-size: 1.2rem;
        color: #667eea;
    }

    .action-btn span {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .action-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .action-btn.disabled:hover {
        transform: none;
        border-color: #e2e8f0;
        background: white;
    }

    .page-title {
        color: white;
        margin-bottom: 2rem;
    }

    .page-title h2 {
        font-size: 2rem;
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .page-title p {
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .search-wrapper {
            flex-direction: column;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="page-title">
    <h2>Management Dashboard</h2>
    <p>Manage devices, vehicles, users, and drivers</p>
</div>

<!-- Search -->
<div class="search-container">
    <form action="{{ route('dashboard') }}" method="GET">
        <div class="search-wrapper">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="q" class="search-input" placeholder="Search by name, ID, plate number..." value="{{ request('q') }}">
            </div>
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
                Search
            </button>
        </div>
    </form>
    <p class="search-hint">Search across all devices, vehicles, users, and drivers</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Devices -->
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Devices</p>
                <p class="stat-number">{{ $stats['devices']['total'] ?? 0 }}</p>
            </div>
            <div class="stat-icon devices">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
        <div class="stat-details">
            <span class="stat-detail"><span class="dot active"></span> {{ $stats['devices']['active'] ?? 0 }} Active</span>
            <span class="stat-detail"><span class="dot inactive"></span> {{ $stats['devices']['inactive'] ?? 0 }} Inactive</span>
        </div>
        <div class="progress-bar">
            @php
                $devicePercent = ($stats['devices']['total'] ?? 0) > 0 
                    ? (($stats['devices']['active'] ?? 0) / $stats['devices']['total']) * 100 
                    : 0;
            @endphp
            <div class="progress-fill devices" style="width: {{ $devicePercent }}%"></div>
        </div>
        <a href="{{ route('management.devices.index') }}" class="manage-link">
            Manage Devices <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Vehicles -->
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Vehicles</p>
                <p class="stat-number">{{ $stats['vehicles']['total'] ?? 0 }}</p>
            </div>
            <div class="stat-icon vehicles">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-details">
            <span class="stat-detail"><span class="dot active"></span> {{ $stats['vehicles']['active'] ?? 0 }} Active</span>
            <span class="stat-detail"><span class="dot maintenance"></span> {{ $stats['vehicles']['maintenance'] ?? 0 }} Maintenance</span>
        </div>
        <div class="progress-bar">
            @php
                $vehiclePercent = ($stats['vehicles']['total'] ?? 0) > 0 
                    ? (($stats['vehicles']['active'] ?? 0) / $stats['vehicles']['total']) * 100 
                    : 0;
            @endphp
            <div class="progress-fill vehicles" style="width: {{ $vehiclePercent }}%"></div>
        </div>
        <a href="{{ route('management.vehicles.index') }}" class="manage-link">
            Manage Vehicles <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Users -->
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Users</p>
                <p class="stat-number">{{ $stats['users']['total'] ?? 0 }}</p>
            </div>
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-details">
            <span class="stat-detail"><span class="dot active"></span> {{ $stats['users']['active'] ?? 0 }} Active</span>
            <span class="stat-detail"><span class="dot admin"></span> {{ $stats['users']['admins'] ?? 0 }} Admins</span>
        </div>
        <div class="progress-bar">
            @php
                $userPercent = ($stats['users']['total'] ?? 0) > 0 
                    ? (($stats['users']['active'] ?? 0) / $stats['users']['total']) * 100 
                    : 0;
            @endphp
            <div class="progress-fill users" style="width: {{ $userPercent }}%"></div>
        </div>
        <a href="{{ route('management.users.index') }}" class="manage-link">
            Manage Users <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Drivers -->
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Drivers</p>
                <p class="stat-number">{{ $stats['drivers']['total'] ?? 0 }}</p>
            </div>
            <div class="stat-icon drivers">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
        <div class="stat-details">
            <span class="stat-detail"><span class="dot active"></span> {{ $stats['drivers']['active'] ?? 0 }} Active</span>
            <span class="stat-detail"><span class="dot leave"></span> {{ $stats['drivers']['on_leave'] ?? 0 }} On Leave</span>
        </div>
        <div class="progress-bar">
            @php
                $driverPercent = ($stats['drivers']['total'] ?? 0) > 0 
                    ? (($stats['drivers']['active'] ?? 0) / $stats['drivers']['total']) * 100 
                    : 0;
            @endphp
            <div class="progress-fill drivers" style="width: {{ $driverPercent }}%"></div>
        </div>
        <a href="{{ route('management.drivers.index') }}" class="manage-link">
            Manage Drivers <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
    <div class="actions-grid">
        <a href="{{ route('management.devices.create') }}" class="action-btn">
            <i class="fas fa-plus-circle"></i>
            <span>Add Device</span>
        </a>
        <a href="{{ route('management.vehicles.create') }}" class="action-btn">
            <i class="fas fa-truck"></i>
            <span>Add Vehicle</span>
        </a>
        <a href="{{ route('management.users.create') }}" class="action-btn">
            <i class="fas fa-user-plus"></i>
            <span>Add User</span>
        </a>
        <a href="{{ route('management.drivers.create') }}" class="action-btn">
            <i class="fas fa-id-card"></i>
            <span>Add Driver</span>
        </a>
        <a href="#" class="action-btn disabled" title="Coming soon">
            <i class="fas fa-map-marked-alt"></i>
            <span>Live Map</span>
        </a>
        <a href="#" class="action-btn disabled" title="Coming soon">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
        </a>
    </div>
</div>
@endsection
