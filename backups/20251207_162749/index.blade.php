@extends('layouts.app')

@section('title', 'Management Dashboard')

@section('content')
<div class="page-wrapper">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Management Dashboard</h1>
            <p class="page-subtitle">Manage devices, vehicles, users and drivers.</p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('hub') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Hub</span>
            </a>
        </div>
    </div>

    {{-- Global search --}}
    <div class="card card-search">
        <div class="card-body">
            <form method="GET" action="{{ route('management.index') }}" class="search-form">
                <div class="search-row">
                    <div class="search-input-wrapper">
                        <span class="search-icon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="search-input"
                            placeholder="Search by name, ID, plate number, IMEI, email, license number..."
                            value="{{ $searchQuery }}"
                            autofocus
                        >
                    </div>

                    <div class="search-actions">
                        <button type="submit" class="btn btn-primary search-button">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>

                        @if ($searchQuery)
                            <a href="{{ route('management.index') }}" class="btn btn-ghost search-clear">
                                <i class="fas fa-times"></i>
                                <span>Clear</span>
                            </a>
                        @endif
                    </div>
                </div>
                <p class="search-hint">
                    Search across all <strong>devices</strong>, <strong>vehicles</strong>, <strong>users</strong> and <strong>drivers</strong>.
                </p>
            </form>
        </div>
    </div>

    {{-- Search results --}}
    @if($searchResults && $searchQuery)
        <div class="results-header">
            <h2 class="results-title">
                Results for <span class="results-query">"{{ $searchQuery }}"</span>
            </h2>
            <span class="badge badge-primary">
                {{ $searchResults['total_found'] }} found
            </span>
        </div>

        @if($searchResults['total_found'] > 0)
            <div class="results-grid">
                {{-- Devices --}}
                @if($searchResults['devices']->count() > 0)
                    <div class="card results-card">
                        <div class="results-card-header devices">
                            <div class="results-card-title">
                                <i class="fas fa-microchip"></i>
                                <span>Devices ({{ $searchResults['devices']->count() }})</span>
                            </div>
                            <a href="{{ route('management.devices.index', ['search' => $searchQuery]) }}" class="btn btn-light btn-xs">
                                View all
                            </a>
                        </div>
                        <div class="results-list">
                            @foreach($searchResults['devices'] as $device)
                                <a href="{{ route('management.devices.edit', $device) }}" class="results-item">
                                    <div class="results-item-main">
                                        <div class="results-item-title">{{ $device->name }}</div>
                                        <div class="results-item-subtitle">
                                            IMEI: {{ $device->imei }}
                                            @if($device->vehicle)
                                                · Vehicle: {{ $device->vehicle->plate_number }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge {{ $device->status === 'active' ? 'badge-success' : 'badge-muted' }}">
                                        {{ ucfirst($device->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Vehicles --}}
                @if($searchResults['vehicles']->count() > 0)
                    <div class="card results-card">
                        <div class="results-card-header vehicles">
                            <div class="results-card-title">
                                <i class="fas fa-car"></i>
                                <span>Vehicles ({{ $searchResults['vehicles']->count() }})</span>
                            </div>
                            <a href="{{ route('management.vehicles.index', ['search' => $searchQuery]) }}" class="btn btn-light btn-xs">
                                View all
                            </a>
                        </div>
                        <div class="results-list">
                            @foreach($searchResults['vehicles'] as $vehicle)
                                <a href="{{ route('management.vehicles.edit', $vehicle) }}" class="results-item">
                                    <div class="results-item-main">
                                        <div class="results-item-title">{{ $vehicle->plate_number }}</div>
                                        <div class="results-item-subtitle">
                                            {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})
                                            @if($vehicle->primaryDriver)
                                                · Driver: {{ $vehicle->primaryDriver->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge {{ $vehicle->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($vehicle->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Users --}}
                @if($searchResults['users']->count() > 0)
                    <div class="card results-card">
                        <div class="results-card-header users">
                            <div class="results-card-title">
                                <i class="fas fa-users"></i>
                                <span>Users ({{ $searchResults['users']->count() }})</span>
                            </div>
                            <a href="{{ route('management.users.index', ['search' => $searchQuery]) }}" class="btn btn-light btn-xs">
                                View all
                            </a>
                        </div>
                        <div class="results-list">
                            @foreach($searchResults['users'] as $user)
                                <a href="{{ route('management.users.edit', $user) }}" class="results-item">
                                    <div class="results-item-main">
                                        <div class="results-item-title">{{ $user->name }}</div>
                                        <div class="results-item-subtitle">
                                            {{ $user->email }}
                                            @if($user->roles->first())
                                                · Role: {{ ucfirst($user->roles->first()->name) }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-muted' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Drivers --}}
                @if($searchResults['drivers']->count() > 0)
                    <div class="card results-card">
                        <div class="results-card-header drivers">
                            <div class="results-card-title">
                                <i class="fas fa-id-card"></i>
                                <span>Drivers ({{ $searchResults['drivers']->count() }})</span>
                            </div>
                            <a href="{{ route('management.drivers.index', ['search' => $searchQuery]) }}" class="btn btn-light btn-xs">
                                View all
                            </a>
                        </div>
                        <div class="results-list">
                            @foreach($searchResults['drivers'] as $driver)
                                <a href="{{ route('management.drivers.edit', $driver) }}" class="results-item">
                                    <div class="results-item-main">
                                        <div class="results-item-title">{{ $driver->name }}</div>
                                        <div class="results-item-subtitle">
                                            License: {{ $driver->license_number }} ({{ $driver->license_type }})
                                            @if($driver->vehicles->count() > 0)
                                                · Vehicles: {{ $driver->vehicles->pluck('plate_number')->join(', ') }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge {{ $driver->status === 'active' ? 'badge-success' : 'badge-muted' }}">
                                        {{ ucfirst($driver->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="card card-empty">
                <div class="card-body">
                    <p class="empty-text">
                        <i class="fas fa-info-circle"></i>
                        No results found for <strong>"{{ $searchQuery }}"</strong>. Try a different search term.
                    </p>
                </div>
            </div>
        @endif
    @endif

    {{-- Stats + quick actions (no search) --}}
    @if(!$searchQuery)
        <div class="stats-grid">
            {{-- Devices --}}
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Devices</div>
                    <div class="stat-icon devices">
                        <i class="fas fa-microchip"></i>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value">{{ $stats['devices']['total'] }}</div>
                    <div class="stat-meta">
                        <span class="badge badge-success">{{ $stats['devices']['active'] }} Active</span>
                        <span class="badge badge-muted">{{ $stats['devices']['inactive'] }} Inactive</span>
                    </div>
                </div>
                <a href="{{ route('management.devices.index') }}" class="btn btn-primary btn-sm stat-button">
                    Manage devices
                </a>
            </div>

            {{-- Vehicles --}}
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Vehicles</div>
                    <div class="stat-icon vehicles">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value">{{ $stats['vehicles']['total'] }}</div>
                    <div class="stat-meta">
                        <span class="badge badge-success">{{ $stats['vehicles']['active'] }} Active</span>
                        <span class="badge badge-warning">{{ $stats['vehicles']['maintenance'] }} Maintenance</span>
                    </div>
                </div>
                <a href="{{ route('management.vehicles.index') }}" class="btn btn-primary btn-sm stat-button">
                    Manage vehicles
                </a>
            </div>

            {{-- Users --}}
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Users</div>
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value">{{ $stats['users']['total'] }}</div>
                    <div class="stat-meta">
                        <span class="badge badge-success">{{ $stats['users']['active'] }} Active</span>
                        <span class="badge badge-danger">{{ $stats['users']['admins'] }} Admins</span>
                    </div>
                </div>
                <a href="{{ route('management.users.index') }}" class="btn btn-primary btn-sm stat-button">
                    Manage users
                </a>
            </div>

            {{-- Drivers --}}
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Drivers</div>
                    <div class="stat-icon drivers">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value">{{ $stats['drivers']['total'] }}</div>
                    <div class="stat-meta">
                        <span class="badge badge-success">{{ $stats['drivers']['active'] }} Active</span>
                        <span class="badge badge-warning">{{ $stats['drivers']['on_leave'] }} On leave</span>
                    </div>
                </div>
                <a href="{{ route('management.drivers.index') }}" class="btn btn-primary btn-sm stat-button">
                    Manage drivers
                </a>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card quick-card">
            <div class="card-body">
                <h2 class="quick-title">
                    <i class="fas fa-bolt" style="margin-right:6px;"></i>
                    Quick actions
                </h2>
                <div class="quick-grid">
                    @can('devices.add')
                        <a href="{{ route('management.devices.create') }}" class="quick-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add new device</span>
                        </a>
                    @endcan

                    @can('vehicles.add')
                        <a href="{{ route('management.vehicles.create') }}" class="quick-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add new vehicle</span>
                        </a>
                    @endcan

                    @can('users.add')
                        <a href="{{ route('management.users.create') }}" class="quick-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add new user</span>
                        </a>
                    @endcan

                    @can('drivers.add')
                        <a href="{{ route('management.drivers.create') }}" class="quick-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add new driver</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 16px 40px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
    }

    .page-header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
        color: #111827;
    }

    .page-subtitle {
        font-size: 14px;
        margin-top: 4px;
        color: #6b7280;
    }

    .card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow:
            0 18px 45px rgba(124, 58, 237, 0.2),
            0 0 0 1px rgba(148, 163, 184, 0.18);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .card-body {
        padding: 18px 18px 16px;
    }

    .card-search {
        margin-bottom: 22px;
    }

    .card-empty {
        margin-top: 14px;
    }

    .empty-text {
        font-size: 14px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .empty-text i {
        color: #3b82f6;
    }

    .btn {
        border-radius: 999px;
        padding: 0.5rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
        transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s, transform 0.1s;
    }
    .btn i { font-size: 0.9rem; }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 10px 25px rgba(37,99,235,0.35);
    }

    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #fff;
        color: #111827;
        border-color: #e5e7eb;
    }

    .btn-secondary:hover {
        background: #f9fafb;
    }

    .btn-light {
        background: #f3f4f6;
        color: #111827;
        border-color: #e5e7eb;
    }

    .btn-light:hover {
        background: #e5e7eb;
    }

    .btn-ghost {
        background: transparent;
        color: #6b7280;
    }

    .btn-ghost:hover {
        background: #f3f4f6;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-xs {
        padding: 4px 10px;
        font-size: 12px;
        border-radius: 999px;
    }

    /* Global search layout */
    .search-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .search-row {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .search-input-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        border-radius: 999px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 0 12px;
    }

    .search-icon {
        margin-right: 8px;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-input {
        border: none;
        background: transparent;
        font-size: 14px;
        padding: 10px 4px;
        width: 100%;
        outline: none;
    }

    .search-hint {
        margin-top: 0;
        font-size: 12px;
        color: #9ca3af;
    }

    .search-actions {
        display: flex;
        flex-direction: row;
        gap: 8px;
        flex-shrink: 0;
    }

    .search-button {
        min-width: 110px;
    }

    .search-clear {
        font-size: 13px;
    }

    .results-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .results-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .results-query {
        color: #4f46e5;
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 8px;
    }

    .results-card-header {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .results-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .results-card-title i {
        font-size: 14px;
    }

    .results-list {
        padding: 2px 0 4px;
        max-height: 320px;
        overflow-y: auto;
    }

    .results-item {
        padding: 9px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        text-decoration: none;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.12s, transform 0.08s;
    }

    .results-item:last-child {
        border-bottom: none;
    }

    .results-item:hover {
        background: #f9fafb;
        transform: translateY(-1px);
    }

    .results-item-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .results-item-title {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .results-item-subtitle {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .badge-primary {
        background: #eef2ff;
        border-color: #e0e7ff;
        color: #4338ca;
    }

    .badge-success {
        background: #ecfdf3;
        border-color: #bbf7d0;
        color: #166534;
    }

    .badge-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }

    .badge-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .badge-muted {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #4b5563;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 8px;
        margin-bottom: 18px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 55px rgba(129,140,248,0.2);
        padding: 14px 14px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6b7280;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #ffffff;
    }

    .stat-icon.devices {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }

    .stat-icon.vehicles {
        background: linear-gradient(135deg, #10b981, #22c55e);
    }

    .stat-icon.users {
        background: linear-gradient(135deg, #0ea5e9, #22d3ee);
    }

    .stat-icon.drivers {
        background: linear-gradient(135deg, #facc15, #f97316);
    }

    .stat-main {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    .stat-meta {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .stat-button {
        margin-top: 6px;
        justify-content: center;
    }

    .quick-card {
        margin-top: 4px;
    }

    .quick-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 10px;
        display: flex;
        align-items: center;
        color: #111827;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .quick-btn {
        border-radius: 999px;
        border: 1px dashed #e5e7eb;
        background: #f9fafb;
        padding: 9px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #111827;
        text-decoration: none;
        transition: background 0.12s, border-color 0.12s, box-shadow 0.12s, transform 0.08s;
    }

    .quick-btn i {
        color: #2563eb;
    }

    .quick-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18);
        transform: translateY(-1px);
    }

    .results-card-header.devices .results-card-title i {
        color: #6366f1;
    }
    .results-card-header.vehicles .results-card-title i {
        color: #10b981;
    }
    .results-card-header.users .results-card-title i {
        color: #0ea5e9;
    }
    .results-card-header.drivers .results-card-title i {
        color: #f59e0b;
    }

    @media (max-width: 1024px) {
        .results-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .quick-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .search-layout {
            flex-direction: column;
            align-items: stretch;
        }
        .search-actions {
            justify-content: flex-start;
        }
        .stats-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .quick-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endsection
