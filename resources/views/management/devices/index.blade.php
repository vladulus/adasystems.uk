@extends('layouts.app')

@section('title', 'Devices')

@section('content')
<div class="page-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Devices</h1>
            <p class="page-subtitle">Manage tracking devices, Serials numbers and assignments.</p>
        </div>

        <div class="page-header-actions">
            {{-- Dashboard button --}}
            <a href="{{ route('management.index') }}" class="btn btn-ghost">
                <i class="fas fa-th-large" style="margin-right:6px;"></i>
                Dashboard
            </a>

            {{-- Search --}}
            <form action="{{ route('management.devices.index') }}" method="GET" class="devices-search-form">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name, serial, vehicle..."
                    class="input input-search"
                >
                <button type="submit" class="btn btn-light">
                    Search
                </button>
            </form>

            {{-- Add device --}}
            <a href="{{ route('management.devices.create') }}" class="btn btn-primary">
                + Add Device
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card filters-card">
        <form action="{{ route('management.devices.index') }}" method="GET" class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="input">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Assignment</label>
                <select name="assignment" class="input">
                    <option value="">All</option>
                    <option value="assigned" {{ request('assignment') === 'assigned' ? 'selected' : '' }}>Assigned to vehicle</option>
                    <option value="unassigned" {{ request('assignment') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Created from</label>
                <input
                    type="date"
                    name="created_from"
                    value="{{ request('created_from') }}"
                    class="input"
                >
            </div>

            <div class="filter-group">
                <label class="filter-label">Created to</label>
                <input
                    type="date"
                    name="created_to"
                    value="{{ request('created_to') }}"
                    class="input"
                >
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-primary btn-sm w-100">Apply filters</button>
                <a href="{{ route('management.devices.index') }}" class="btn btn-ghost btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>

    {{-- Main content --}}
    <div class="content-grid">
        {{-- Devices list --}}
        <div class="card devices-card">
            <div class="devices-card-header">
                <h2 class="section-title">Devices list</h2>
                @if(isset($devices) && $devices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <span class="section-meta">
                        Showing {{ $devices->firstItem() }}–{{ $devices->lastItem() }} of {{ $devices->total() }} devices
                    </span>
                @endif
            </div>

            <div class="devices-table">
                <div class="devices-table-head">
                    <div class="col-name">Device & Serial</div>
                    <div class="col-vehicle">Vehicle</div>
                    <div class="col-status">Status</div>
                    <div class="col-created">Created</div>
                    <div class="col-actions">Actions</div>
                </div>

                @forelse($devices as $device)
                    <div class="devices-table-row">
                        <div class="col-name">
                            <div class="avatar-square">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="device-main">
                                <div class="device-name">{{ $device->name ?? 'Unnamed device' }}</div>
                                <div class="device-meta">
                                    Serial number: {{ $device->serial_number ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-vehicle">
                            @php
                                $plate = optional($device->vehicle)->registration_number ?? null;
                            @endphp
                            @if($plate)
                                <span class="badge badge-vehicle">{{ $plate }}</span>
                            @else
                                <span class="badge badge-muted">Unassigned</span>
                            @endif
                        </div>

                        <div class="col-status">
                            @php $status = $device->status ?? 'inactive'; @endphp
                            @if($status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($status === 'maintenance')
                                <span class="badge badge-warning">Maintenance</span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </div>

                        <div class="col-created">
                            @if(isset($device->created_at) && $device->created_at)
                                {{ $device->created_at->format('Y-m-d') }}
                            @else
                                —
                            @endif
                        </div>

                        <div class="col-actions">
                            <a href="{{ route('management.devices.edit', $device) }}" class="btn btn-light btn-xs">
                                Edit
                            </a>

                            <form
                                action="{{ route('management.devices.destroy', $device) }}"
                                method="POST"
                                style="display:inline-block;"
                                onsubmit="return confirm('Are you sure you want to delete this device?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="devices-empty">
                        <p>No devices found with the current filters.</p>
                    </div>
                @endforelse
            </div>

            <div class="devices-pagination">
                {{ $devices->links() }}
            </div>
        </div>

        {{-- Overview --}}
        <div class="card overview-card">
            <h2 class="section-title">Overview</h2>

            @php
                $devStats = $stats['devices'] ?? [
                    'total' => $devices->total() ?? 0,
                    'active' => 0,
                    'inactive' => 0,
                    'maintenance' => 0,
                ];
            @endphp

            <div class="overview-grid">
                <div class="overview-item">
                    <span class="label">Total devices</span>
                    <span class="value">{{ $devStats['total'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Active</span>
                    <span class="value text-green">{{ $devStats['active'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Inactive</span>
                    <span class="value">{{ $devStats['inactive'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Maintenance</span>
                    <span class="value text-orange">{{ $devStats['maintenance'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-wrapper {
        max-width: 1200px;
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

    .page-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .page-subtitle {
        font-size: 14px;
        margin-top: 4px;
        color: #6b7280;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .devices-search-form {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .input {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 8px 10px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
        min-width: 0;
    }

    .input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.25);
    }

    .input-search {
        min-width: 220px;
    }

    .btn {
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s, transform 0.1s;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        transform: translateY(-1px);
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
        color: #4b5563;
        border-color: transparent;
    }

    .btn-ghost:hover {
        background: #f3f4f6;
        border-color: #e5e7eb;
    }

    .btn-danger {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    .btn-danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        box-shadow: 0 10px 18px rgba(239, 68, 68, 0.25);
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 13px;
    }

    .btn-xs {
        padding: 4px 9px;
        font-size: 12px;
    }

    .w-100 {
        width: 100%;
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

    .filters-card {
        padding: 14px 16px 12px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px 16px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
    }

    .filters-actions {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 2.2fr) minmax(260px, 1fr);
        gap: 16px;
    }

    .devices-card {
        padding: 12px 0 8px;
    }

    .devices-card-header {
        padding: 0 18px 8px;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .section-meta {
        font-size: 12px;
        color: #9ca3af;
    }

    .devices-table {
        border-top: 1px solid #e5e7eb;
    }

    .devices-table-head,
    .devices-table-row {
        display: grid;
        grid-template-columns: 2.6fr 1.3fr 1.1fr 1.2fr 1fr;
        padding: 10px 18px;
        align-items: center;
        column-gap: 10px;
    }

    .devices-table-head {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9ca3af;
        background: #f9fafb;
    }

    .devices-table-row {
        border-top: 1px solid #f3f4f6;
        font-size: 13px;
    }

    .devices-table-row:nth-child(even) {
        background: #fcfcff;
    }

    .devices-table-row:hover {
        background: #f5f7ff;
    }

    .col-name {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .avatar-square {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0ea5e9, #6366f1);
        color: #ffffff;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .device-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .device-name {
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .device-meta {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .col-vehicle,
    .col-status,
    .col-created,
    .col-actions {
        font-size: 13px;
    }

    .col-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .devices-empty {
        padding: 16px 18px;
        font-size: 14px;
        color: #6b7280;
    }

    .devices-pagination {
        padding: 10px 18px 6px;
        font-size: 13px;
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

    .badge-vehicle {
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

    .badge-muted {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #4b5563;
    }

    .overview-card {
        padding: 14px 16px 12px;
    }

    .overview-grid {
        margin-top: 10px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .overview-item {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 10px 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .overview-item .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
    }

    .overview-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    .text-green {
        color: #16a34a;
    }

    .text-orange {
        color: #f97316;
    }

    @media (max-width: 1024px) {
        .filters-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .content-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .devices-search-form {
            flex: 1;
        }

        .filters-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .devices-table-head,
        .devices-table-row {
            grid-template-columns: minmax(0, 1fr);
            row-gap: 6px;
        }

        .col-actions {
            justify-content: flex-start;
        }

        .overview-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endsection
