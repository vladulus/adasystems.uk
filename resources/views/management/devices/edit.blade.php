@extends('layouts.app')

@section('title', 'Edit Device')

@section('content')
<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit device</h1>
            <p class="page-subtitle">
                {{ $device->name ?? 'Device' }} – Serial number: {{ $device->serial_number ?? '—' }}
            </p>
        </div>

        <div class="page-header-actions">
            {{-- Dashboard --}}
            <a href="{{ route('management.index') }}" class="btn btn-ghost">
                <i class="fas fa-th-large" style="margin-right:6px;"></i>
                Dashboard
            </a>

            {{-- Back to devices --}}
            <a href="{{ route('management.devices.index') }}" class="btn btn-light">
                ← Back to devices
            </a>
        </div>
    </div>

    <div class="card form-card">
        <form action="{{ route('management.devices.update', $device) }}" method="POST" class="device-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- Device details --}}
                <div class="form-section">
                    <h2 class="section-title">Device details</h2>

                    <div class="field-group">
                        <label class="field-label">Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $device->name) }}"
                            class="input @error('name') input-error @enderror"
                            required
                        >
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Serial number (optional)</label>
                        <input
                            type="text"
                            name="serial_number"
                            value="{{ old('serial_number', $device->serial_number ?? null) }}"
                            class="input @error('serial_number') input-error @enderror"
                        >
                        @error('serial_number')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Notes (optional)</label>
                        <textarea
                            name="notes"
                            rows="3"
                            class="input textarea @error('notes') input-error @enderror"
                        >{{ old('notes', $device->notes ?? null) }}</textarea>
                        @error('notes')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Assignment & status --}}
                <div class="form-section">
                    <h2 class="section-title">Assignment & status</h2>

                    @isset($vehicles)
                    <div class="field-group">
                        <label class="field-label">Assigned vehicle</label>
                        <select name="vehicle_id" class="input @error('vehicle_id') input-error @enderror">
                            <option value="">Unassigned</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ (int) old('vehicle_id', $device->vehicle_id) === (int) $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_number }} – {{ $vehicle->make }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    @endisset

                    <div class="field-group">
                        <label class="field-label">Status</label>
                        <select name="status" class="input @error('status') input-error @enderror">
                            @php $status = old('status', $device->status ?? 'active'); @endphp
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ $status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Tag (optional)</label>
                        <input
                            type="text"
                            name="tag"
                            value="{{ old('tag', $device->tag ?? null) }}"
                            class="input @error('tag') input-error @enderror"
                        >
                        @error('tag')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('management.devices.index') }}" class="btn btn-ghost">
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
        max-width: 900px;
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
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
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

    .form-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow:
            0 18px 45px rgba(124, 58, 237, 0.2),
            0 0 0 1px rgba(148, 163, 184, 0.18);
        padding: 18px 18px 16px;
    }

    .device-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
        gap: 16px;
    }

    .form-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 4px;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .field-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
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

    .input.textarea {
        resize: vertical;
        min-height: 80px;
    }

    .input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.25);
    }

    .input-error {
        border-color: #ef4444;
    }

    .field-error {
        font-size: 12px;
        color: #b91c1c;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 8px;
    }

    @media (max-width: 900px) {
        .form-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endsection
