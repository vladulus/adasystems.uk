@extends('layouts.app')

@section('title', 'Edit Permissions')

@section('content')
<div class="page-wrapper">
    {{-- Header --}}
    <div class="header-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Edit Permissions – {{ $user->name }}</h1>
                <p class="page-subtitle">Configure granular permissions for {{ $user->email }}</p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('management.users.index') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Permissions Form --}}
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('management.permissions.update', $user) }}">
                @csrf
                @method('PUT')

                {{-- TOP ROW: Settings (left) | Dashboard (right) --}}
                <div class="permissions-row-top">
                    {{-- LEFT COLUMN: Settings --}}
                    <div class="permissions-col-left">
                        <div class="perm-card">
                            <div class="perm-card-header">
                                <div class="perm-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div>
                                    <h3 class="perm-title">Settings</h3>
                                    <span class="perm-count">1 permission</span>
                                </div>
                            </div>

                            <div class="perm-list">
                                <label class="perm-item">
                                    <input type="checkbox" name="permissions[]" value="settings.access"
                                        @if(in_array('settings.access', $userPermissions)) checked @endif>
                                    <span class="checkmark"></span>
                                    <span class="perm-label">Access</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Dashboard --}}
                    <div class="permissions-col-right">
                        <div class="perm-card perm-card-tall">
                            <div class="perm-card-header">
                                <div class="perm-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div>
                                    <h3 class="perm-title">Dashboard</h3>
                                    <span class="perm-count">{{ isset($groupedPermissions['dashboard']) ? count(array_filter($groupedPermissions['dashboard'], fn($p) => !str_contains($p['name'], '.scope.') && $p['name'] !== 'dashboard.access')) : 0 }} permissions</span>
                                </div>
                            </div>

                            <div class="perm-list">
                                @if(isset($groupedPermissions['dashboard']))
                                    @foreach($groupedPermissions['dashboard'] as $permission)
                                        @if(!str_contains($permission['name'], '.scope.') && $permission['name'] !== 'dashboard.access')
                                            <label class="perm-item">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission['name'] }}"
                                                    @if(in_array($permission['name'], $userPermissions)) checked @endif>
                                                <span class="checkmark"></span>
                                                <span class="perm-label">{{ ucfirst($permission['action'] ?: $permission['name']) }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                            {{-- Dashboard Scope --}}
                            <div class="scope-section">
                                <div class="scope-label">Data Scope</div>
                                <div class="scope-options">
                                    <label class="scope-item">
                                        <input type="radio" name="dashboard_scope" value="none"
                                            @if(!in_array('dashboard.scope.own', $userPermissions) && !in_array('dashboard.scope.all', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>No access</span>
                                    </label>
                                    <label class="scope-item">
                                        <input type="radio" name="dashboard_scope" value="own"
                                            @if(in_array('dashboard.scope.own', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>Own only</span>
                                    </label>
                                    <label class="scope-item">
                                        <input type="radio" name="dashboard_scope" value="all"
                                            @if(in_array('dashboard.scope.all', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>All</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTTOM ROW: Devices, Vehicles, Users, Drivers --}}
                <div class="permissions-row-bottom">
                    @foreach(['devices', 'vehicles', 'users', 'drivers'] as $category)
                        <div class="perm-card">
                            <div class="perm-card-header">
                                <div class="perm-icon">
                                    @switch($category)
                                        @case('devices')
                                            <i class="fas fa-microchip"></i>
                                            @break
                                        @case('vehicles')
                                            <i class="fas fa-car"></i>
                                            @break
                                        @case('users')
                                            <i class="fas fa-users"></i>
                                            @break
                                        @case('drivers')
                                            <i class="fas fa-id-card"></i>
                                            @break
                                    @endswitch
                                </div>
                                <div>
                                    <h3 class="perm-title">{{ ucfirst($category) }}</h3>
                                    <span class="perm-count">{{ isset($groupedPermissions[$category]) ? count(array_filter($groupedPermissions[$category], fn($p) => !str_contains($p['name'], '.scope.'))) : 0 }} permissions</span>
                                </div>
                            </div>

                            <div class="perm-list">
                                @if(isset($groupedPermissions[$category]))
                                    @foreach($groupedPermissions[$category] as $permission)
                                        @if(!str_contains($permission['name'], '.scope.'))
                                            <label class="perm-item">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission['name'] }}"
                                                    @if(in_array($permission['name'], $userPermissions)) checked @endif>
                                                <span class="checkmark"></span>
                                                <span class="perm-label">{{ ucfirst($permission['action'] ?: $permission['name']) }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                            {{-- Scope --}}
                            <div class="scope-section">
                                <div class="scope-label">Data Scope</div>
                                <div class="scope-options">
                                    <label class="scope-item">
                                        <input type="radio" name="{{ $category }}_scope" value="none"
                                            @if(!in_array($category . '.scope.own', $userPermissions) && !in_array($category . '.scope.all', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>No access</span>
                                    </label>
                                    <label class="scope-item">
                                        <input type="radio" name="{{ $category }}_scope" value="own"
                                            @if(in_array($category . '.scope.own', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>Own only</span>
                                    </label>
                                    <label class="scope-item">
                                        <input type="radio" name="{{ $category }}_scope" value="all"
                                            @if(in_array($category . '.scope.all', $userPermissions)) checked @endif>
                                        <span class="radio-mark"></span>
                                        <span>All</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="form-actions">
                    <a href="{{ route('management.users.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Convert radio buttons to hidden permission inputs before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const scopes = ['dashboard', 'devices', 'vehicles', 'users', 'drivers'];
    
    scopes.forEach(scope => {
        const radio = document.querySelector(`input[name="${scope}_scope"]:checked`);
        if (radio && radio.value !== 'none') {
            const permName = scope + '.scope.' + radio.value;
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'permissions[]';
            hidden.value = permName;
            this.appendChild(hidden);
        }
    });
});
</script>

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
        color: #111827;
    }

    .page-subtitle {
        font-size: 14px;
        margin-top: 4px;
        color: #6b7280;
    }

    .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 18px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 14px;
    }

    .alert ul {
        margin: 0;
        padding-left: 0;
        list-style: none;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow: 0 18px 45px rgba(124, 58, 237, 0.2), 0 0 0 1px rgba(148, 163, 184, 0.18);
        overflow: hidden;
    }

    .card-body {
        padding: 24px;
    }

    /* TOP ROW LAYOUT */
    .permissions-row-top {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .permissions-col-left {
        display: flex;
        flex-direction: column;
    }

    .permissions-col-right {
        display: flex;
        flex-direction: column;
    }

    /* BOTTOM ROW LAYOUT */
    .permissions-row-bottom {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    /* CARD STYLES */
    .perm-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 18px;
        background: #fafafa;
        transition: all 0.2s;
    }

    .perm-card:hover {
        border-color: #a78bfa;
        background: #faf5ff;
    }

    .perm-card-tall {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .perm-card-tall .perm-list {
        flex: 1;
    }

    .perm-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid #e5e7eb;
    }

    .perm-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }

    .perm-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #1f2937;
    }

    .perm-count {
        font-size: 12px;
        color: #9ca3af;
    }

    /* PERMISSION ITEM STYLES */
    .perm-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .perm-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        padding: 8px 10px;
        border-radius: 8px;
        transition: background 0.2s;
        margin: 0;
    }

    .perm-item:hover {
        background: #f3f4f6;
    }

    .perm-item input[type="checkbox"] {
        display: none;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .checkmark::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 11px;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.15s;
    }

    .perm-item input[type="checkbox"]:checked + .checkmark {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        border-color: #7c3aed;
    }

    .perm-item input[type="checkbox"]:checked + .checkmark::after {
        opacity: 1;
        transform: scale(1);
    }

    .perm-label {
        flex: 1;
    }

    /* SCOPE STYLES */
    .scope-section {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #e5e7eb;
    }

    .scope-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .scope-options {
        display: flex;
        gap: 6px;
    }

    .scope-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #374151;
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 8px;
        background: #f3f4f6;
        border: 2px solid transparent;
        transition: all 0.2s;
        flex: 1;
        justify-content: center;
    }

    .scope-item:hover {
        background: #e5e7eb;
    }

    .scope-item:has(input:checked) {
        background: #ede9fe;
        border-color: #7c3aed;
        color: #5b21b6;
    }

    .scope-item input[type="radio"] {
        display: none;
    }

    .radio-mark {
        width: 14px;
        height: 14px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .radio-mark::after {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.15s;
    }

    .scope-item input[type="radio"]:checked + .radio-mark {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        border-color: #7c3aed;
    }

    .scope-item input[type="radio"]:checked + .radio-mark::after {
        opacity: 1;
        transform: scale(1);
    }

    /* FORM ACTIONS */
    .form-actions {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-light {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-light:hover {
        background: #e5e7eb;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.4);
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .permissions-row-bottom {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .permissions-row-top {
            grid-template-columns: 1fr;
        }

        .permissions-row-bottom {
            grid-template-columns: 1fr;
        }

        .scope-options {
            flex-direction: column;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn-light,
        .form-actions .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection