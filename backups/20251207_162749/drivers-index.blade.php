@extends('layouts.app')

@section('title', 'Drivers')

@section('styles')
<style>
.page-header {
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:1.5rem;
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
.drivers-table {
    border-radius:14px;
    border:1px solid #eef2ff;
    overflow:hidden;
}
.drivers-table-head,
.drivers-table-row {
    display:grid;
    grid-template-columns:2.6fr 2fr 1.1fr 1.3fr 1.4fr;
    align-items:center;
}
.drivers-table-head {
    background:#f9fafb;
    border-bottom:1px solid #e5e7eb;
    padding:0.55rem 0.9rem;
    font-size:0.75rem;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#9ca3af;
    font-weight:600;
}
.drivers-table-row {
    padding:0.65rem 0.9rem;
    border-top:1px solid #f3f4f6;
}
.drivers-table-row:nth-child(even) {
    background:#fcfcff;
}
.col-driver {
    display:flex;
    align-items:center;
    gap:0.7rem;
}
.avatar-square {
    width:32px;
    height:32px;
    border-radius:10px;
    background:radial-gradient(circle at 0 0,#22c55e,#16a34a);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:0.9rem;
}
.driver-name {
    font-size:0.9rem;
    font-weight:600;
    color:#111827;
}
.driver-meta {
    font-size:0.78rem;
    color:#6b7280;
}
.col-license {
    font-size:0.8rem;
    color:#4b5563;
}
.license-meta {
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
.badge-danger-soft {
    background:#fee2e2;
    color:#b91c1c;
}
.badge-muted {
    background:#eef2ff;
    color:#4b5563;
}

.inline-form { display:inline; }

.drivers-empty {
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
<div class="page-header">
    <div>
        <h1 class="page-title">Drivers</h1>
        <p class="page-subtitle">
            Manage drivers, licenses and vehicle assignments.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('management.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>

        @can('create', App\Models\Driver::class)
            <a href="{{ route('management.drivers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Add driver</span>
            </a>
        @endcan
    </div>
</div>

{{-- Search card --}}
<div class="card card-search">
    <form action="{{ route('management.drivers.index') }}" method="GET" class="search-form">
        <div class="search-row">
            <div class="search-input-wrapper">
                <span class="search-icon">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Search by name, email, phone, license..."
                    value="{{ request('search') }}"
                >
            </div>
            <div class="search-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('management.drivers.index') }}" class="btn btn-ghost">
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
    <form action="{{ route('management.drivers.index') }}" method="GET" class="filters-row">
        <div class="filter-field">
            <label class="filter-label">Status</label>
            <select name="status" class="select">
                <option value="">All statuses</option>
                <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
                <option value="inactive"   {{ request('status') === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                <option value="on_leave"   {{ request('status') === 'on_leave'   ? 'selected' : '' }}>On leave</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>

        <div class="filter-field">
            <label class="filter-label">Assignment</label>
            <select name="assignment" class="select">
                <option value="">All</option>
                <option value="assigned"   {{ request('assignment') === 'assigned'   ? 'selected' : '' }}>With vehicle</option>
                <option value="unassigned" {{ request('assignment') === 'unassigned' ? 'selected' : '' }}>No vehicle</option>
            </select>
        </div>

        <div class="filter-field">
            <label class="filter-label">License</label>
            <select name="license_status" class="select">
                <option value="">All</option>
                <option value="valid"          {{ request('license_status') === 'valid'          ? 'selected' : '' }}>Valid</option>
                <option value="expiring_soon"  {{ request('license_status') === 'expiring_soon'  ? 'selected' : '' }}>Expiring soon</option>
                <option value="expired"        {{ request('license_status') === 'expired'        ? 'selected' : '' }}>Expired</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-light">Apply filters</button>
            <a href="{{ route('management.drivers.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="content-grid">
    {{-- Drivers list --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Drivers list</h2>
            <span class="card-meta">
                Showing {{ $drivers->firstItem() ?? 0 }}–{{ $drivers->lastItem() ?? 0 }}
                of {{ $drivers->total() }} drivers
            </span>
        </div>

        <div class="drivers-table">
            <div class="drivers-table-head">
                <div class="col-driver">Driver</div>
                <div class="col-license">License</div>
                <div class="col-status">Status</div>
                <div class="col-created">Created</div>
                <div class="col-actions">Actions</div>
            </div>

            @forelse($drivers as $driver)
                <div class="drivers-table-row">
                    {{-- Driver --}}
                    <div class="col-driver">
                        <div class="avatar-square">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <div class="driver-name">
                                {{ $driver->name ?? 'Driver' }}
                            </div>
                            <div class="driver-meta">
                                @if($driver->email)
                                    {{ $driver->email }}
                                @elseif($driver->phone)
                                    {{ $driver->phone }}
                                @elseif($driver->user)
                                    {{ $driver->user->email }}
                                @else
                                    No contact details
                                @endif
                                @if($driver->phone)
                                    · {{ $driver->phone }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- License --}}
                    <div class="col-license">
                        <div>
                            {{ $driver->license_number ?? 'No license' }}
                            @if($driver->license_type)
                                · {{ $driver->license_type }}
                            @endif
                        </div>
                        @php
                            $expiry = $driver->license_expiry_date;
                        @endphp
                        @if($expiry)
                            <div class="license-meta">
                                @if($expiry < now())
                                    <span class="badge badge-danger-soft">
                                        <i class="fas fa-exclamation-triangle"></i> Expired {{ $expiry->format('M d, Y') }}
                                    </span>
                                @elseif($expiry->between(now(), now()->addDays(30)))
                                    <span class="badge badge-warning">
                                        Expires {{ $expiry->diffForHumans(null, true) }}
                                    </span>
                                @else
                                    Expires {{ $expiry->format('M d, Y') }}
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="col-status">
                        @php
                            $status = $driver->status ?? 'inactive';
                            $statusClass = match($status) {
                                'active'   => 'badge-success',
                                'on_leave' => 'badge-warning',
                                'terminated' => 'badge-danger-soft',
                                default    => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                    </div>

                    {{-- Created --}}
                    <div class="col-created">
                        {{ $driver->created_at?->format('M d, Y') ?? '—' }}
                    </div>

                    {{-- Actions --}}
                    <div class="col-actions">
                        @can('update', $driver)
                            <a href="{{ route('management.drivers.edit', $driver) }}" class="btn-icon-small">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                        @endcan

                        @can('delete', $driver)
                            <form action="{{ route('management.drivers.destroy', $driver) }}"
                                  method="POST"
                                  class="inline-form"
                                  onsubmit="return confirm('Delete this driver? This action cannot be undone.');">
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
                <div class="drivers-empty">
                    <p>No drivers found. Try adjusting your filters.</p>
                </div>
            @endforelse
        </div>

        @if($drivers->hasPages())
            <div class="pagination-wrapper">
                {{ $drivers->links() }}
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
                <div class="stat-label">On leave</div>
                <div class="stat-value text-warning">{{ $stats['on_leave'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Terminated</div>
                <div class="stat-value text-muted">{{ $stats['terminated'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">With vehicle</div>
                <div class="stat-value">{{ $stats['with_vehicles'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">No vehicle</div>
                <div class="stat-value">{{ $stats['without_vehicles'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">License expired</div>
                <div class="stat-value text-warning">{{ $stats['license_expired'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">License expiring soon</div>
                <div class="stat-value text-warning">{{ $stats['license_expiring_soon'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Average age</div>
                @php $avgAge = $stats['avg_age'] ?? null; @endphp
                <div class="stat-value">{{ $avgAge ? round($avgAge) . ' yrs' : '—' }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Hired this year</div>
                <div class="stat-value">{{ $stats['hired_this_year'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
