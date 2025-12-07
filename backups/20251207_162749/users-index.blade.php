@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="page-wrapper">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Users</h1>
            <p class="page-subtitle">Manage platform users, roles and access.</p>
        </div>

        <div class="page-header-actions">
            {{-- Dashboard button --}}
            <a href="{{ route('management.index') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>

            {{-- Add user --}}
            <a href="{{ route('management.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Add User</span>
            </a>
        </div>
    </div>

    {{-- Search card --}}
    <div class="card card-search">
        <form action="{{ route('management.users.index') }}" method="GET" class="search-form">
            <div class="search-row">
                <div class="search-input-wrapper">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="Search by name, email, phone..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('management.users.index') }}" class="btn btn-ghost">
                            <i class="fas fa-times"></i>
                            <span>Clear</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
	    @if (session('error'))
        <div style="margin-bottom:10px;">
            <div style="
                display:inline-flex;
                align-items:center;
                padding:8px 12px;
                border-radius:999px;
                background:#fee2e2;
                color:#b91c1c;
                font-size:13px;
            ">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card filters-card">
        <form action="{{ route('management.users.index') }}" method="GET" class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Role</label>
                <select name="role" class="input">
                    <option value="">All roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="input">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Email verification</label>
                <select name="email_verified" class="input">
                    <option value="">All</option>
                    <option value="verified" {{ request('email_verified') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('email_verified') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Department</label>
                <select name="department" class="input">
                    <option value="">All</option>
                    @foreach($departments as $dep)
                        <option value="{{ $dep }}" {{ request('department') === $dep ? 'selected' : '' }}>
                            {{ $dep }}
                        </option>
                    @endforeach
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

            <div class="filter-group">
                <label class="filter-label">Last login</label>
                <select name="last_login" class="input">
                    <option value="">Any time</option>
                    <option value="today" {{ request('last_login') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('last_login') === 'week' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="month" {{ request('last_login') === 'month' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="never" {{ request('last_login') === 'never' ? 'selected' : '' }}>Never</option>
                </select>
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-primary btn-sm w-100">Apply filters</button>
                <a href="{{ route('management.users.index') }}" class="btn btn-ghost btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>

    {{-- Main content: users list + overview --}}
    <div class="content-grid">
        {{-- Users list --}}
        <div class="card users-card">
            <div class="users-card-header">
                <h2 class="section-title">Users list</h2>
                <span class="section-meta">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
                </span>
            </div>

            <div class="users-table">
                <div class="users-table-head">
                    <div class="col-name">Name & email</div>
                    <div class="col-role">Role</div>
                    <div class="col-status">Status</div>
                    <div class="col-last-login">Last login</div>
                    <div class="col-created">Created</div>
                    <div class="col-actions">Actions</div>
                </div>

                @forelse($users as $user)
                    <div class="users-table-row">
                        <div class="col-name">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="user-main">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="col-role">
                            @if($user->roles->first())
                                <span class="badge badge-role">
                                    {{ ucfirst($user->roles->first()->name) }}
                                </span>
                            @else
                                <span class="badge badge-muted">No role</span>
                            @endif
                        </div>

                        <div class="col-status">
                            @if($user->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge badge-muted">Inactive</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </div>

                        <div class="col-last-login">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </div>

                        <div class="col-created">
                            {{ $user->created_at->format('Y-m-d') }}
                        </div>

                      <div class="col-actions">
                          @if($user->isRoot())
                              {{-- Pentru contul root (vlad@impulsive.ro) NU arătăm niciun buton --}}
                              <span class="badge badge-muted">Root</span>
                          @else
                              {{-- PERM button – vizibil mereu, autorizarea se face în controller --}}
                              <a href="{{ route('management.permissions.edit', $user) }}" class="btn btn-warning btn-xs">
                                  PERM
                              </a>

                              {{-- Edit button – vizibil mereu, autorizarea se face în controller / middleware --}}
                              <a href="{{ route('management.users.edit', $user) }}" class="btn btn-light btn-xs">
                                  Edit
                              </a>

                              {{-- Delete button – vizibil mereu, dar nu la propriul cont --}}
                              @if(auth()->id() !== $user->id)
                                  <form
                                      action="{{ route('management.users.destroy', $user) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');"
                                  >
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-danger btn-xs">
                                          Delete
                                      </button>
                                  </form>
                              @endif
                          @endif
                      </div>  
                    </div>
                @empty
                    <div class="users-empty">
                        <p>No users found with the current filters.</p>
                    </div>
                @endforelse
            </div>

            <div class="users-pagination">
                {{ $users->links() }}
            </div>
        </div>

        {{-- Overview --}}
        <div class="card overview-card">
            <h2 class="section-title">Overview</h2>

            <div class="overview-grid">
                <div class="overview-item">
                    <span class="label">Total users</span>
                    <span class="value">{{ $stats['total'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Active</span>
                    <span class="value text-green">{{ $stats['active'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Inactive</span>
                    <span class="value">{{ $stats['inactive'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Pending</span>
                    <span class="value text-orange">{{ $stats['pending'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Admins</span>
                    <span class="value">{{ $stats['admins'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Verified emails</span>
                    <span class="value">{{ $stats['email_verified'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Logged in today</span>
                    <span class="value">{{ $stats['logged_in_today'] }}</span>
                </div>
                <div class="overview-item">
                    <span class="label">Created this month</span>
                    <span class="value">{{ $stats['created_this_month'] }}</span>
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

    .users-search-form {
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

    .btn-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .btn-warning:hover {
        background: #fde68a;
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

    .users-card {
        padding: 12px 0 8px;
    }

    .users-card-header {
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

    .users-table {
        border-top: 1px solid #e5e7eb;
    }

    .users-table-head,
    .users-table-row {
        display: grid;
        grid-template-columns: 2fr 1fr 0.9fr 1.1fr 1.1fr 1.8fr;
        padding: 10px 18px;
        align-items: center;
        column-gap: 10px;
    }

    .users-table-head {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9ca3af;
        background: #f9fafb;
    }

    .users-table-row {
        border-top: 1px solid #f3f4f6;
        font-size: 13px;
    }

    .users-table-row:nth-child(even) {
        background: #fcfcff;
    }

    .users-table-row:hover {
        background: #f5f7ff;
    }

    .col-name {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: linear-gradient(135deg, #a855f7, #6366f1);
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .user-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .user-name {
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .col-role,
    .col-status,
    .col-last-login,
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

    .users-empty {
        padding: 16px 18px;
        font-size: 14px;
        color: #6b7280;
    }

    .users-pagination {
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

    .badge-role {
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

        .users-search-form {
            flex: 1;
        }

        .filters-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .users-table-head,
        .users-table-row {
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
