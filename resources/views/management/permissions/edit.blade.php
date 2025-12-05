@extends('layouts.app')

@section('title', 'Edit Permissions')

@section('content')
<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Edit Permissions – {{ $user->name }}
            </h1>
            <p class="page-subtitle">
                Setează permisiunile granulare pentru utilizatorul {{ $user->email }}.
            </p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('management.users.index') }}" class="btn btn-ghost">
                &larr; Users list
            </a>
        </div>
    </div>

    <div class="card" style="padding: 16px 18px 18px;">
        @if (session('status'))
            <div class="mb-3">
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-3">
                <div class="alert alert-danger">
                    <ul class="mb-0" style="padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 13px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('management.permissions.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="permissions-grid">
                @forelse ($groupedPermissions as $category => $permissions)
                    <div class="perm-card">
                        <div class="perm-card-header">
                            <h3 class="perm-title">
                                {{ ucfirst($category) }}
                            </h3>
                            <span class="perm-subtitle">
                                Category permissions
                            </span>
                        </div>

                        <div class="perm-list">
                            @foreach ($permissions as $permission)
                                <label class="perm-item">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission['name'] }}"
                                        @if (in_array($permission['name'], $userPermissions)) checked @endif
                                    >
                                    <span class="perm-label">
                                        {{ $permission['action'] !== '' ? $permission['action'] : $permission['name'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p style="font-size: 14px; color:#6b7280;">
                        Nu există permisiuni pe care le poți acorda acestui utilizator.
                    </p>
                @endforelse
            </div>

            <div style="margin-top: 18px; display:flex; justify-content:flex-end; gap:8px;">
                <a href="{{ route('management.users.index') }}" class="btn btn-light btn-sm">
                    Anulează
                </a>

                <button type="submit" class="btn btn-primary btn-sm">
                    Salvează permisiunile
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .perm-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 10px 12px 10px;
        background: #f9fafb;
    }

    .perm-card-header {
        margin-bottom: 6px;
    }

    .perm-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
    }

    .perm-subtitle {
        font-size: 11px;
        color: #9ca3af;
    }

    .perm-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 6px;
    }

    .perm-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #374151;
    }

    .perm-item input[type="checkbox"] {
        width: 14px;
        height: 14px;
    }

    .perm-label {
        text-transform: none;
    }

    @media (max-width: 768px) {
        .permissions-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endsection
