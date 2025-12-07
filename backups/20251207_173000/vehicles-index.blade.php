@extends('layouts.app')

@section('title', 'Vehicles')

@section('styles')
<style>
.page-wrapper {
    max-width:1400px;
    margin:0 auto;
    padding:24px 16px 40px;
}
.header-card {
    background:#fff;
    border-radius:18px;
    padding:16px 20px;
    margin-bottom:18px;
    box-shadow:0 18px 55px rgba(129,140,248,0.3);
}
.page-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
}
.page-title {
    font-size:1.75rem;
    font-weight:700;
    color:#111827;
}
.page-subtitle {
    font-size:0.9rem;
    color:#6b7280;
    margin-top:0.25rem;
}
.page-header-actions {
    display:flex;
    gap:0.75rem;
}

/* Buttons */
.btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:0.35rem;
    border-radius:999px;
    padding:0.5rem 1.1rem;
    font-size:0.85rem;
    font-weight:500;
    border:1px solid transparent;
    cursor:pointer;
    text-decoration:none;
    transition:.15s ease;
    white-space:nowrap;
}
.btn i { font-size:0.9rem; }
.btn-primary {
    background:#2563eb;
    color:#fff;
    box-shadow:0 10px 25px rgba(37,99,235,0.35);
}
.btn-primary:hover { background:#1d4ed8; transform:translateY(-1px); }
.btn-secondary {
    background:#fff;
    color:#111827;
    border-color:#e5e7eb;
}
.btn-secondary:hover { background:#f9fafb; }
.btn-ghost {
    background:transparent;
    color:#6b7280;
}
.btn-icon {
    padding-inline:0.9rem;
}
.btn-icon-small {
    display:inline-flex;
    align-items:center;
    gap:0.25rem;
    padding:0.25rem 0.5rem;
    border-radius:999px;
    border:1px solid #e5e7eb;
    background:#fff;
    font-size:0.75rem;
    color:#374151;
    text-decoration:none;
}
.btn-icon-small i { font-size:0.8rem; }
.btn-icon-small:hover { background:#f9fafb; }
.btn-danger {
    border-color:#fecaca;
    color:#b91c1c;
}
.btn-danger:hover {
    background:#fef2f2;
}

/* Filters */
.filters-card {
    background:#fff;
    border-radius:18px;
    padding:1rem 1.25rem;
    margin-bottom:1.5rem;
    box-shadow:0 14px 40px rgba(129,140,248,0.25);
}

/* Search card */
.card-search {
    background:#fff;
    border-radius:18px;
    padding:16px 20px;
    margin-bottom:1rem;
    box-shadow:0 18px 55px rgba(129,140,248,0.3);
    overflow:visible;
}
.search-form {
    display:flex;
    flex-direction:column;
    gap:0;
}
.search-row {
    display:flex;
    gap:12px;
    align-items:center;
}
.search-input-wrapper {
    flex:1;
    display:flex;
    align-items:center;
    border-radius:999px;
    background:#f9fafb;
    border:1px solid #e5e7eb;
    padding:0 12px;
}
.search-icon {
    margin-right:8px;
    color:#9ca3af;
    display:flex;
    align-items:center;
}
.search-input {
    border:none;
    background:transparent;
    font-size:14px;
    padding:10px 4px;
    width:100%;
    outline:none;
}
.search-actions {
    display:flex;
    gap:8px;
    flex-shrink:0;
}
.search-input-container {
    flex:1;
    position:relative;
}
.search-spinner {
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    color:#9ca3af;
}
.autocomplete-dropdown {
    position:absolute;
    top:100%;
    left:0;
    right:0;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    box-shadow:0 10px 40px rgba(0,0,0,0.15);
    margin-top:4px;
    z-index:1000;
    max-height:400px;
    overflow-y:auto;
}
.autocomplete-results {
    padding:8px 0;
}
.autocomplete-item {
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 16px;
    text-decoration:none;
    color:inherit;
    transition:background 0.15s;
}
.autocomplete-item:hover {
    background:#f3f4f6;
}
.autocomplete-item-icon {
    width:32px;
    height:32px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:14px;
    flex-shrink:0;
}
.autocomplete-item-content {
    flex:1;
    min-width:0;
}
.autocomplete-item-title {
    font-weight:500;
    color:#111827;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
.autocomplete-item-subtitle {
    font-size:12px;
    color:#6b7280;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
.autocomplete-item-badge {
    font-size:11px;
    padding:2px 8px;
    border-radius:999px;
    font-weight:500;
}
.autocomplete-item-badge.active {
    background:#dcfce7;
    color:#166534;
}
.autocomplete-item-badge.inactive {
    background:#f3f4f6;
    color:#6b7280;
}
.autocomplete-empty {
    padding:16px;
    text-align:center;
    color:#6b7280;
    font-size:14px;
}

.filters-row {
    display:flex;
    flex-wrap:wrap;
    gap:0.75rem;
    align-items:flex-end;
}
.filter-field {
    min-width:170px;
}
.filter-label {
    display:block;
    font-size:0.75rem;
    font-weight:600;
    text-transform:uppercase;
    color:#9ca3af;
    margin-bottom:0.25rem;
}
.select {
    width:100%;
    border-radius:999px;
    border:1px solid #e5e7eb;
    padding:0.45rem 0.75rem;
    font-size:0.85rem;
}
.filter-actions {
    display:flex;
    gap:0.5rem;
    margin-left:auto;
}

/* Layout cards */
.content-grid {
    display:grid;
    grid-template-columns:minmax(0,3fr) minmax(0,1.5fr);
    gap:1.5rem;
}
.card {
    background:#fff;
    border-radius:18px;
    box-shadow:0 18px 55px rgba(129,140,248,0.3);
    padding:1.25rem 1.5rem;
}
.card-side { min-width:260px; }
.card-header {
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    margin-bottom:1rem;
}
.card-title {
    font-size:1rem;
    font-weight:600;
    color:#111827;
}
.card-meta {
    font-size:0.75rem;
    color:#9ca3af;
}

/* Table */
.vehicles-table {
    border-radius:14px;
    border:1px solid #eef2ff;
    overflow:hidden;
}
.vehicles-table-head,
.vehicles-table-row {
    display:grid;
    grid-template-columns:2.6fr 2fr 1.1fr 1.3fr 1.4fr;
    align-items:center;
}
.vehicles-table-head {
    background:#f9fafb;
    border-bottom:1px solid #e5e7eb;
    padding:0.55rem 0.9rem;
    font-size:0.75rem;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#9ca3af;
    font-weight:600;
}
.vehicles-table-row {
    padding:0.65rem 0.9rem;
    border-top:1px solid #f3f4f6;
}
.vehicles-table-row:nth-child(even) {
    background:#fcfcff;
}
.col-vehicle {
    display:flex;
    align-items:center;
    gap:0.7rem;
}
.avatar-square {
    width:32px;
    height:32px;
    border-radius:10px;
    background:radial-gradient(circle at 0 0,#a855f7,#6366f1);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:0.9rem;
}
.vehicle-name {
    font-size:0.9rem;
    font-weight:600;
    color:#111827;
}
.vehicle-meta {
    font-size:0.78rem;
    color:#6b7280;
}
.col-device {
    font-size:0.8rem;
    color:#4b5563;
}
.device-name { font-weight:500; }
.device-meta {
    font-size:0.75rem;
    color:#9ca3af;
}
.col-status,
.col-created,
.col-actions {
    font-size:0.8rem;
}
.col-actions {
    display:flex;
    gap:0.35rem;
    justify-content:flex-end;
    flex-wrap:nowrap;
}

/* Badges */
.badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:999px;
    padding:0.2rem 0.6rem;
    font-size:0.7rem;
    font-weight:600;
}
.badge-success {
    background:#dcfce7;
    color:#166534;
}
.badge-secondary {
    background:#e5e7eb;
    color:#374151;
}
.badge-warning {
    background:#fef3c7;
    color:#92400e;
}
.badge-muted {
    background:#eef2ff;
    color:#4b5563;
}

.inline-form { display:inline; }

.vehicles-empty {
    padding:1rem;
    text-align:center;
    font-size:0.9rem;
    color:#6b7280;
}

/* Stats */
.stats-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:0.75rem;
}
.stat-item {
    padding:0.6rem 0.7rem;
    border-radius:12px;
    background:#f9fafb;
}
.stat-label {
    font-size:0.75rem;
    color:#6b7280;
}
.stat-value {
    font-size:0.95rem;
    font-weight:600;
    color:#111827;
}
.text-success { color:#16a34a; }
.text-warning { color:#ea580c; }
.text-muted   { color:#9ca3af; }

.pagination-wrapper { margin-top:0.75rem; }

@media (max-width:1024px) {
    .content-grid {
        grid-template-columns:1fr;
    }
}
@media (max-width:768px) {
    .filters-row { flex-direction:column; align-items:stretch; }
    .filter-actions { margin-left:0; }
    .page-header { flex-direction:column; gap:0.75rem; }
}
</style>
@endsection

@section('content')
<div class="page-wrapper">
    <!-- Header -->
    <div class="header-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Vehicles</h1>
                <p class="page-subtitle">
                    Manage fleet vehicles, serialised devices and assignments.
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('management.index') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>

                @can('create', App\Models\Vehicle::class)
                    <a href="{{ route('management.vehicles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add vehicle</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Search card --}}
<div class="card card-search">
    <form action="{{ route('management.vehicles.index') }}" method="GET" class="search-form" id="searchForm">
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
                        placeholder="Search by plate, model, VIN, device..."
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
                    <a href="{{ route('management.vehicles.index') }}" class="btn btn-ghost">
                        <i class="fas fa-times"></i>
                        <span>Clear</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Filters --}}
<div class="filters-card">
    <form action="{{ route('management.vehicles.index') }}" method="GET" class="filters-row">
        <div class="filter-field">
            <label class="filter-label">Status</label>
            <select name="status" class="select">
                <option value="">All statuses</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="service"  {{ request('status') === 'service'  ? 'selected' : '' }}>In service</option>
            </select>
        </div>

        <div class="filter-field">
            <label class="filter-label">Assignment</label>
            <select name="assignment" class="select">
                <option value="">All</option>
                <option value="with_device"   {{ request('assignment') === 'with_device'   ? 'selected' : '' }}>With device</option>
                <option value="without_device" {{ request('assignment') === 'without_device' ? 'selected' : '' }}>No device</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-light">Apply filters</button>
            <a href="{{ route('management.vehicles.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="content-grid">
    {{-- Vehicles list --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Vehicles list</h2>
            <span class="card-meta">
                Showing {{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}
                of {{ $vehicles->total() }} vehicles
            </span>
        </div>

        <div class="vehicles-table">
            <div class="vehicles-table-head">
                <div class="col-vehicle">Vehicle</div>
                <div class="col-device">Device</div>
                <div class="col-status">Status</div>
                <div class="col-created">Created</div>
                <div class="col-actions">Actions</div>
            </div>

            @forelse($vehicles as $vehicle)
                <div class="vehicles-table-row">
                    {{-- Vehicle --}}
                    <div class="col-vehicle">
                        <div class="avatar-square">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <div>
                            <div class="vehicle-name">
                                {{ $vehicle->registration_number ?? 'Unregistered' }}
                            </div>
                            <div class="vehicle-meta">
                                {{ trim(($vehicle->make ?? '').' '.($vehicle->model ?? '')) ?: 'No make / model' }}
                                @if($vehicle->year)
                                    · {{ $vehicle->year }}
                                @endif
                                @if($vehicle->vin)
                                    · VIN: {{ $vehicle->vin }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Device --}}
                    <div class="col-device">
                        @if($vehicle->device)
                            <div class="device-name">{{ $vehicle->device->device_name ?? 'Device' }}</div>
                            @if($vehicle->device->serial_number)
                                <div class="device-meta">Serial: {{ $vehicle->device->serial_number }}</div>
                            @endif
                        @else
                            <span class="badge badge-muted">No device</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="col-status">
                        @php
                            $status = $vehicle->status ?? 'inactive';
                            $statusClass = match($status) {
                                'active'  => 'badge-success',
                                'service' => 'badge-warning',
                                default   => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                    </div>

                    {{-- Created --}}
                    <div class="col-created">
                        {{ $vehicle->created_at?->format('M d, Y') ?? '—' }}
                    </div>

                    {{-- Actions --}}
                    <div class="col-actions">
                        @can('update', $vehicle)
                            <a href="{{ route('management.vehicles.edit', $vehicle) }}" class="btn-icon-small">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                        @endcan

                        @can('delete', $vehicle)
                            <form action="{{ route('management.vehicles.destroy', $vehicle) }}"
                                  method="POST"
                                  class="inline-form"
                                  onsubmit="return confirm('Delete this vehicle? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-small btn-danger">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="vehicles-empty">
                    <p>No vehicles found.</p>
                </div>
            @endforelse
        </div>

        @if($vehicles->hasPages())
            <div class="pagination-wrapper">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>

    {{-- Overview --}}
    <div class="card card-side">
        <div class="card-header">
            <h2 class="card-title">Overview</h2>
        </div>

        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Active</div>
                <div class="stat-value text-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Inactive</div>
                <div class="stat-value text-muted">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">In service</div>
                <div class="stat-value text-warning">{{ $stats['service'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">With device</div>
                <div class="stat-value">{{ $stats['with_device'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">No device</div>
                <div class="stat-value">{{ $stats['no_device'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>
</div>
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

        fetch(`{{ route('management.autocomplete.vehicles') }}?q=${encodeURIComponent(query)}`, {
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
            results.innerHTML = '<div class="autocomplete-empty"><i class="fas fa-search" style="margin-right:8px;opacity:0.5;"></i>No vehicles found</div>';
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
