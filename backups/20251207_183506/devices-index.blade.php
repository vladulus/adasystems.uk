@extends('layouts.app')

@section('title', 'Devices')

@section('content')
<div class="page-wrapper">
    <!-- Header -->
    <div class="header-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Devices</h1>
                <p class="page-subtitle">Manage tracking devices, Serials numbers and assignments.</p>
            </div>

            <div class="page-header-actions">
                {{-- Dashboard button --}}
                <a href="{{ route('management.index') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Add device --}}
                <a href="{{ route('management.devices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Add Device</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Search card --}}
    <div class="card card-search">
        <form action="{{ route('management.devices.index') }}" method="GET" class="search-form" id="searchForm">
            <div class="search-row">
                <div class="search-input-container">
                    <div class="search-input-wrapper">
                        <span class="search-icon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            class="search-input"
                            placeholder="Search by name, serial number, IMEI, vehicle..."
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                        <span class="search-spinner" id="searchSpinner" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </div>
                    {{-- Autocomplete dropdown --}}
                    <div class="autocomplete-dropdown" id="autocompleteDropdown" style="display:none;">
                        <div class="autocomplete-results" id="autocompleteResults"></div>
                    </div>
                </div>
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('management.devices.index') }}" class="btn btn-ghost">
                            <i class="fas fa-times"></i>
                            <span>Clear</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
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
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 16px 40px;
    }

    .header-card {
        padding: 16px 20px;
        margin-bottom: 18px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 18px 45px rgba(124, 58, 237, 0.2), 0 0 0 1px rgba(148, 163, 184, 0.18);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
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
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        padding: 8px 12px;
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
        transition: 0.15s ease;
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
        background: #f9fafb;
    }

    .btn-icon {
        padding-inline: 0.9rem;
    }

    .btn-danger {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .btn-danger:hover {
        background: #fecaca;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-xs {
        padding: 4px 10px;
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

    .card-search {
        padding: 16px 20px;
        overflow: visible;
    }

    .search-form {
        display: flex;
        flex-direction: column;
        gap: 0;
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
    }

    .search-input {
        border: none;
        background: transparent;
        font-size: 14px;
        padding: 10px 4px;
        width: 100%;
        outline: none;
    }

    .search-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .search-input-container {
        flex: 1;
        position: relative;
    }

    .search-spinner {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        margin-top: 4px;
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
    }

    .autocomplete-results {
        padding: 8px 0;
    }

    .autocomplete-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }

    .autocomplete-item:hover {
        background: #f3f4f6;
    }

    .autocomplete-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 14px;
        flex-shrink: 0;
    }

    .autocomplete-item-content {
        flex: 1;
        min-width: 0;
    }

    .autocomplete-item-title {
        font-weight: 500;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .autocomplete-item-subtitle {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .autocomplete-item-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 500;
    }

    .autocomplete-item-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .autocomplete-item-badge.inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    .autocomplete-empty {
        padding: 16px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
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
        grid-template-columns: 2.4fr 1.3fr 1fr 1.2fr 1.5fr;
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
        flex-wrap: nowrap;
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const dropdown = document.getElementById('autocompleteDropdown');
    const results = document.getElementById('autocompleteResults');
    const spinner = document.getElementById('searchSpinner');
    const searchForm = document.getElementById('searchForm');
    
    if (!searchInput) return;
    
    let debounceTimer;
    let currentRequest = null;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchResults(query);
        }, 300);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && results.innerHTML) {
            dropdown.style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function fetchResults(query) {
        if (currentRequest) {
            currentRequest.abort();
        }

        spinner.style.display = 'block';

        const controller = new AbortController();
        currentRequest = controller;

        fetch(`{{ route('management.autocomplete.devices') }}?q=${encodeURIComponent(query)}`, {
            signal: controller.signal
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = 'none';
            renderResults(data.results);
            dropdown.style.display = 'block';
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                spinner.style.display = 'none';
                console.error('Search error:', err);
            }
        });
    }

    function renderResults(items) {
        if (!items || items.length === 0) {
            results.innerHTML = '<div class="autocomplete-empty"><i class="fas fa-search" style="margin-right:8px;opacity:0.5;"></i>No devices found</div>';
            return;
        }

        let html = '';
        items.forEach(item => {
            const statusClass = item.status === 'active' ? 'active' : 'inactive';
            html += `
                <a href="${item.url}" class="autocomplete-item">
                    <div class="autocomplete-item-icon" style="background:${item.color}">
                        <i class="fas ${item.icon}"></i>
                    </div>
                    <div class="autocomplete-item-content">
                        <div class="autocomplete-item-title">${escapeHtml(item.title)}</div>
                        <div class="autocomplete-item-subtitle">${escapeHtml(item.subtitle || '')}</div>
                    </div>
                    <span class="autocomplete-item-badge ${statusClass}">${item.status || 'unknown'}</span>
                </a>
            `;
        });
        
        results.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection
