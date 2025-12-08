@extends('layouts.app')

@section('title', 'Edit Device')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit device</h1>
            <p class="page-subtitle">{{ $device->device_name }} — Serial: {{ $device->serial_number ?? '—' }}</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('management.devices.index') }}" class="btn btn-light">← Back to devices</a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>There were some problems:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('management.devices.update', $device) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- Left Column: Device details --}}
                <div class="form-column">
                    <h2 class="card-title">Device details</h2>

                    <div class="form-group">
                        <label class="form-label">Device name</label>
                        <input type="text" name="device_name" class="input" value="{{ old('device_name', $device->device_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Serial number</label>
                        <input type="text" name="serial_number" class="input" value="{{ old('serial_number', $device->serial_number) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="input">
                            <option value="active" @selected(old('status', $device->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $device->status) === 'inactive')>Inactive</option>
                            <option value="maintenance" @selected(old('status', $device->status) === 'maintenance')>Maintenance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Upload interval (seconds)</label>
                        <input type="number" name="upload_interval" class="input" value="{{ old('upload_interval', $device->upload_interval ?? 15) }}" min="5" max="300">
                        <p class="field-hint">How often the device sends telemetry data</p>
                    </div>
                </div>

                {{-- Right Column: Assignment --}}
                <div class="form-column">
                    <h2 class="card-title">Assignment</h2>

                    @php
                        $currentUser = auth()->user();
                        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
                        $isAdmin = $currentUser->hasRole('admin');
                    @endphp

                    {{-- Owner (Superuser) --}}
                    <div class="form-group">
                        <label class="form-label">Owner (Client)</label>
                        <select name="owner_id" id="owner_id" class="select2-single" data-placeholder="Select owner...">
                            <option value="">-- No owner --</option>
                            @foreach($allSuperusers ?? [] as $superuser)
                                <option value="{{ $superuser->id }}" @selected(old('owner_id', $device->owner_id) == $superuser->id)>
                                    {{ $superuser->name }} ({{ $superuser->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Client who purchased this device</p>
                    </div>

                    {{-- Assigned Vehicle --}}
                    <div class="form-group">
                        <label class="form-label">Assigned Vehicle</label>
                        <select name="vehicle_id" id="vehicle_id" class="select2-single" data-placeholder="Select vehicle...">
                            <option value="">-- No vehicle --</option>
                            @foreach($vehicles ?? [] as $vehicle)
                                @php
                                    // Verifică dacă vehiculul poate primi acest device
                                    // (fie nu are device, fie are deja acest device, și e al aceluiași owner)
                                    $canAssign = !$vehicle->device_id || $vehicle->device_id === $device->id;
                                    $ownerMatch = true;
                                    if ($device->owner_id && $vehicle->owner_id && $device->owner_id !== $vehicle->owner_id) {
                                        $ownerMatch = false;
                                    }
                                @endphp
                                @if(($canAssign && $ownerMatch) || $vehicle->device_id === $device->id)
                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $device->vehicle?->id) == $vehicle->id)>
                                    {{ $vehicle->registration_number }}
                                    @if($vehicle->make) - {{ $vehicle->make }} {{ $vehicle->model }}@endif
                                    @if($vehicle->owner) ({{ $vehicle->owner->name }})@endif
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="field-hint">Vehicle must belong to the same owner as the device</p>
                    </div>

                    {{-- Managing Admins - doar pentru super-admin --}}
                    @if($isSuperAdmin)
                    <div class="form-group">
                        <label class="form-label">Managing Admins</label>
                        <select name="admin_ids[]" id="admin_ids" class="select2-chips" multiple="multiple" data-placeholder="Search and select admins...">
                            @foreach($allAdmins ?? [] as $admin)
                                <option value="{{ $admin->id }}" @if(in_array($admin->id, $device->admins->pluck('id')->toArray())) selected @endif>
                                    {{ $admin->name }} ({{ $admin->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Admins who can manage this device</p>
                    </div>
                    @endif

                    {{-- Device Info (Read-only) --}}
                    @if($device->last_online)
                    <div class="form-group">
                        <label class="form-label">Last seen</label>
                        <input type="text" class="input" value="{{ $device->last_online->format('d M Y, H:i') }}" disabled readonly>
                    </div>
                    @endif

                    @if($device->ip_address)
                    <div class="form-group">
                        <label class="form-label">IP Address</label>
                        <input type="text" class="input" value="{{ $device->ip_address }}" disabled readonly>
                    </div>
                    @endif

                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('management.devices.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-wrapper { max-width: 1100px; margin: 0 auto; padding: 24px 16px 40px; }
    .page-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
    .page-title { font-size: 24px; font-weight: 600; margin: 0; }
    .page-subtitle { margin: 4px 0 0; font-size: 14px; color: #6b7280; }
    .page-header-actions { display: flex; gap: 12px; }

    .input { border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 14px; width: 100%; background: #fff; }
    .input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 1px rgba(37,99,235,0.1); }
    .input:disabled { background: #f3f4f6; color: #6b7280; }

    .btn { border-radius: 8px; padding: 8px 14px; font-size: 14px; font-weight: 500; border: 1px solid transparent; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.15s; }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.25); }
    .btn-light { background: #f3f4f6; color: #111827; border-color: #e5e7eb; }
    .btn-light:hover { background: #e5e7eb; }
    .btn-ghost { background: transparent; color: #4b5563; }
    .btn-ghost:hover { background: #f3f4f6; }

    .card { background: #fff; border-radius: 18px; border: 1px solid rgba(148,163,184,0.35); box-shadow: 0 18px 45px rgba(124,58,237,0.28); padding: 18px; overflow: visible; }
    .card-title { font-size: 16px; font-weight: 600; margin: 0 0 14px; }

    .alert { padding: 10px 14px; border-radius: 10px; font-size: 14px; margin-bottom: 14px; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .form-column { display: flex; flex-direction: column; gap: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label { font-size: 13px; font-weight: 500; color: #374151; }
    .field-hint { font-size: 12px; color: #6b7280; margin: 0; }
    .form-footer { grid-column: 1 / -1; margin-top: 14px; padding-top: 14px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px; }

    /* Select2 Single */
    .select2-container--default .select2-selection--single { border: 1px solid #d1d5db; border-radius: 8px; height: 38px; padding: 4px 8px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; color: #374151; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: #2563eb; box-shadow: 0 0 0 1px rgba(37,99,235,0.1); }

    /* Select2 Chips */
    .select2-container--default .select2-selection--multiple { border: 1px solid #d1d5db; border-radius: 8px; padding: 4px 6px; min-height: 38px; background: #fff; }
    .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #2563eb; box-shadow: 0 0 0 1px rgba(37,99,235,0.1); }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background: #7c3aed; color: #fff; border: none; border-radius: 6px; padding: 4px 8px; margin: 2px; font-size: 13px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #fff; margin-right: 5px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #fecaca; background: transparent; }
    .select2-dropdown { border-radius: 8px; border: 1px solid #d1d5db; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #7c3aed; }
    .select2-container--default .select2-search--inline .select2-search__field { margin-top: 4px; font-size: 14px; }

    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-chips').select2({
        width: '100%',
        allowClear: true,
        closeOnSelect: false
    });
    
    $('.select2-single').select2({
        width: '100%',
        allowClear: true
    });
});
</script>
@endsection
