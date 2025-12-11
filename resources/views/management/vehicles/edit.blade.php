@extends('layouts.app')

@section('title', 'Edit vehicle')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit vehicle</h1>
            <p class="page-subtitle">
                {{ $vehicle->registration_number ?? 'Vehicle' }}
                @if($vehicle->make || $vehicle->model)
                    — {{ $vehicle->make }} {{ $vehicle->model }}
                @endif
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('management.vehicles.index') }}" class="btn btn-light">← Back to vehicles</a>
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
        <form action="{{ route('management.vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- Left Column: Vehicle details --}}
                <div class="form-column">
                    <h2 class="card-title">Vehicle details</h2>

                    <div class="form-group">
                        <label class="form-label">Registration number (plate)</label>
                        <div style="display:flex;gap:8px;">
                            <input type="text" name="registration_number" id="registration_number" class="input" value="{{ old('registration_number', $vehicle->registration_number) }}" required style="flex:1;">
                            <button type="button" id="dvla-lookup-btn" class="btn btn-light" onclick="dvlaLookup()" title="Refresh from DVLA">
                                <i class="fas fa-sync-alt"></i> DVLA
                            </button>
                        </div>
                        <p class="field-hint" id="dvla-status"></p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Make</label>
                            <input type="text" name="make" id="make" class="input" value="{{ old('make', $vehicle->make) }}" placeholder="Volvo, Scania, Mercedes...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="input" value="{{ old('model', $vehicle->model) }}" placeholder="FH16, R500, Actros...">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" id="year" class="input" value="{{ old('year', $vehicle->year) }}" min="1990" max="{{ date('Y') + 1 }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">VIN</label>
                            <input type="text" name="vin" id="vin" class="input" value="{{ old('vin', $vehicle->vin) }}" maxlength="17">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Colour</label>
                            <input type="text" name="colour" id="colour" class="input" value="{{ old('colour', $vehicle->colour) }}" placeholder="e.g. BLUE">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fuel type</label>
                            <input type="text" name="fuel_type" id="fuel_type" class="input" value="{{ old('fuel_type', $vehicle->fuel_type) }}" placeholder="e.g. DIESEL">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Engine capacity (cc)</label>
                            <input type="number" name="engine_capacity" id="engine_capacity" class="input" value="{{ old('engine_capacity', $vehicle->engine_capacity) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CO2 emissions (g/km)</label>
                            <input type="number" name="co2_emissions" id="co2_emissions" class="input" value="{{ old('co2_emissions', $vehicle->co2_emissions) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="input">
                            <option value="active" @selected(old('status', $vehicle->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $vehicle->status) === 'inactive')>Inactive</option>
                            <option value="service" @selected(old('status', $vehicle->status) === 'service')>In service</option>
                        </select>
                    </div>

                    <h2 class="card-title" style="margin-top:16px;">MOT & Tax</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">MOT status</label>
                            <input type="text" name="mot_status" id="mot_status" class="input" value="{{ old('mot_status', $vehicle->mot_status) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">MOT expiry</label>
                            <input type="date" name="mot_expiry_date" id="mot_expiry_date" class="input" value="{{ old('mot_expiry_date', $vehicle->mot_expiry_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tax status</label>
                            <input type="text" name="tax_status" id="tax_status" class="input" value="{{ old('tax_status', $vehicle->tax_status) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tax due date</label>
                            <input type="date" name="tax_due_date" id="tax_due_date" class="input" value="{{ old('tax_due_date', $vehicle->tax_due_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Euro status</label>
                        <input type="text" name="euro_status" id="euro_status" class="input" value="{{ old('euro_status', $vehicle->euro_status) }}" readonly>
                    </div>
                </div>

                {{-- Right Column: Assignment --}}
                <div class="form-column">
                    <h2 class="card-title">Assignment</h2>

                    @php
                        $currentUser = auth()->user();
                        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
                        $isAdmin = $currentUser->hasRole('admin');
                        $isSuperuser = $currentUser->isSuperuser();
                    @endphp

                    {{-- Owner (Superuser) - doar pentru super-admin și admin --}}
                    @if($isSuperAdmin || $isAdmin)
                    <div class="form-group">
                        <label class="form-label">Owner (Client)</label>
                        <select name="owner_id" id="owner_id" class="select2-single" data-placeholder="Select owner...">
                            <option value="">-- No owner --</option>
                            @foreach($allSuperusers ?? [] as $superuser)
                                <option value="{{ $superuser->id }}" @selected(old('owner_id', $vehicle->owner_id) == $superuser->id)>
                                    {{ $superuser->name }} ({{ $superuser->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Client who owns this vehicle</p>
                    </div>
                    @endif

                    {{-- Device - doar pentru super-admin și admin --}}
                    @if($isSuperAdmin || $isAdmin)
                    <div class="form-group">
                        <label class="form-label">Tracking Device</label>
                        <select name="device_id" id="device_id" class="select2-single" data-placeholder="Select device...">
                            <option value="">-- No device --</option>
                            @foreach($devices ?? [] as $device)
                                @php
                                    // Verifică dacă device-ul poate fi alocat acestui vehicul
                                    // (fie e liber, fie e deja pe acest vehicul, fie e al aceluiași owner)
                                    $canAssign = !$device->vehicle || $device->vehicle->id === $vehicle->id;
                                    if ($vehicle->owner_id && $device->owner_id && $device->owner_id !== $vehicle->owner_id) {
                                        $canAssign = false;
                                    }
                                @endphp
                                @if($canAssign || old('device_id', $vehicle->device_id) == $device->id)
                                <option value="{{ $device->id }}" @selected(old('device_id', $vehicle->device_id) == $device->id)>
                                    {{ $device->device_name }}
                                    @if($device->owner) ({{ $device->owner->name }})@endif
                                    @if($device->vehicle && $device->vehicle->id !== $vehicle->id) [on {{ $device->vehicle->registration_number }}]@endif
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="field-hint">Device must belong to the same owner as the vehicle</p>
                    </div>
                    @else
                    {{-- Superuser vede device-ul dar nu-l poate schimba --}}
                    @if($vehicle->device)
                    <div class="form-group">
                        <label class="form-label">Tracking Device</label>
                        <input type="text" class="input" value="{{ $vehicle->device->device_name }}" disabled readonly>
                        <p class="field-hint">Contact admin to change device assignment</p>
                    </div>
                    @endif
                    @endif

                    {{-- Assigned Drivers --}}
                    <div class="form-group">
                        <label class="form-label">Assigned Drivers</label>
                        <select name="driver_ids[]" id="driver_ids" class="select2-chips" multiple="multiple" data-placeholder="Search and select drivers...">
                            @foreach($drivers ?? [] as $driver)
                                <option value="{{ $driver->id }}" @if(in_array($driver->id, $vehicle->drivers->pluck('id')->toArray())) selected @endif>
                                    {{ $driver->name }}
                                    @if($driver->license_number) - {{ $driver->license_number }}@endif
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Drivers who can operate this vehicle</p>
                    </div>

                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('management.vehicles.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-wrapper { max-width: 1200px; margin: 0 auto; padding: 24px 16px 40px; }
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

    .form-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; }
    .form-column { display: flex; flex-direction: column; gap: 12px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
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

    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
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

async function dvlaLookup() {
    const regInput = document.getElementById('registration_number');
    const btn = document.getElementById('dvla-lookup-btn');
    const status = document.getElementById('dvla-status');
    
    const registration = regInput.value.trim();
    
    if (!registration) {
        status.textContent = 'Please enter a registration number first';
        status.style.color = '#b91c1c';
        return;
    }
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Looking up...';
    status.textContent = 'Searching DVLA database...';
    status.style.color = '#6b7280';
    
    try {
        const response = await fetch('{{ route("management.dvla.vehicle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ registration: registration }),
        });
        
        const result = await response.json();
        
        if (result.success && result.data) {
            const data = result.data;
            
            if (data.make) document.getElementById('make').value = data.make;
            if (data.year) document.getElementById('year').value = data.year;
            if (data.colour) document.getElementById('colour').value = data.colour;
            if (data.fuel_type) document.getElementById('fuel_type').value = data.fuel_type;
            if (data.engine_capacity) document.getElementById('engine_capacity').value = data.engine_capacity;
            if (data.co2_emissions) document.getElementById('co2_emissions').value = data.co2_emissions;
            if (data.mot_status) document.getElementById('mot_status').value = data.mot_status;
            if (data.mot_expiry_date) document.getElementById('mot_expiry_date').value = data.mot_expiry_date;
            if (data.tax_status) document.getElementById('tax_status').value = data.tax_status;
            if (data.tax_due_date) document.getElementById('tax_due_date').value = data.tax_due_date;
            if (data.euro_status) document.getElementById('euro_status').value = data.euro_status;
            
            status.textContent = '✓ Data refreshed from DVLA' + (result.sandbox ? ' (Sandbox mode)' : '');
            status.style.color = '#059669';
        } else {
            status.textContent = result.error || 'Vehicle not found';
            status.style.color = '#b91c1c';
        }
    } catch (error) {
        console.error('DVLA lookup error:', error);
        status.textContent = 'Error connecting to DVLA service';
        status.style.color = '#b91c1c';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt"></i> DVLA';
    }
}
</script>
@endsection
