@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit user</h1>
            <p class="page-subtitle">
                Update account details, role and status.
            </p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('management.users.index') }}" class="btn btn-light">
                ← Back to users
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <div><strong>There were some problems with your input:</strong></div>
            <ul style="margin: 6px 0 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 18px 18px 16px;">
        <form action="{{ route('management.users.update', $user) }}" method="POST" class="form-grid">
            @csrf
            @method('PUT')

            <div class="form-column">
                <h2 class="card-title" style="margin-bottom: 10px;">User details</h2>

                <div class="form-group">
                    <label class="form-label" for="name">Full name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="input"
                        value="{{ old('name', $user->name) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="input"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone (optional)</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        class="input"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+44 7700 900123"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="department">Department (optional)</label>
                    <input
                        id="department"
                        type="text"
                        name="department"
                        class="input"
                        value="{{ old('department', $user->department) }}"
                        placeholder="Operations, Fleet, Dispatch..."
                    >
                </div>
            </div>

            <div class="form-column">
                <h2 class="card-title" style="margin-bottom: 10px;">Access & security</h2>

                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="input" required>
                        @foreach ($roles as $role)
                            <option
                                value="{{ $role->name }}"
                                @selected(old('role', $user->roles->first()?->name) === $role->name)
                            >
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="input" required>
                        <option value="active" @selected(old('status', $user->status) === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected(old('status', $user->status) === 'inactive')>
                            Inactive
                        </option>
                        <option value="pending" @selected(old('status', $user->status) === 'pending')>
                            Pending
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New password (optional)</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="input"
                    >
                    <p class="field-hint">
                        Leave blank to keep the current password.
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="input"
                    >
                </div>

                <div class="form-group form-group-inline">
                    <label class="checkbox">
                        <input
                            type="checkbox"
                            name="email_verified"
                            value="1"
                            {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}
                        >
                        <span>Mark email as verified</span>
                    </label>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('management.users.index') }}" class="btn btn-ghost">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Save changes
                </button>
            </div>
        </form>
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
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .page-subtitle {
        margin: 4px 0 0;
        font-size: 14px;
        color: #6b7280;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        min-width: 0;
        width: 100%;
        background: #ffffff;
    }

    .input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.1);
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
        transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);
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

    .card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow:
            0 18px 45px rgba(124, 58, 237, 0.28),
            0 0 0 1px rgba(148, 163, 184, 0.18);
        overflow: hidden;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .alert {
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 14px;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
        gap: 18px;
        align-items: flex-start;
    }

    .form-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
    }

    .field-hint {
        font-size: 12px;
        color: #6b7280;
        margin: 0;
    }

    .form-group-inline .checkbox {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #4b5563;
        cursor: pointer;
    }

    .form-group-inline input[type="checkbox"] {
        width: 16px;
        height: 16px;
    }

    .form-footer {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @media (max-width: 900px) {
        .form-grid {
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
    }
</style>
@endsection
