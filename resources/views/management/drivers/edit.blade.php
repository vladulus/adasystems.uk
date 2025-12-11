@extends('layouts.app')

@section('title', 'Edit driver')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit driver</h1>
            <p class="page-subtitle">{{ $driver->name }} — License: {{ $driver->license_number }}</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('management.drivers.index') }}" class="btn btn-light">← Back to drivers</a>
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
        <form action="{{ route('management.drivers.update', $driver) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- Left Column: Driver details --}}
                <div class="form-column">
                    <h2 class="card-title">Driver details</h2>

                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="input" value="{{ old('name', $driver->name) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="input" value="{{ old('email', $driver->email) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="input" value="{{ old('phone', $driver->phone) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date of birth</label>
                            <input type="date" name="date_of_birth" class="input" value="{{ old('date_of_birth', optional($driver->date_of_birth)->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hire date</label>
                            <input type="date" name="hire_date" class="input" value="{{ old('hire_date', optional($driver->hire_date)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="input textarea">{{ old('address', $driver->address) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Emergency contact</label>
                        <input type="text" name="emergency_contact" class="input" value="{{ old('emergency_contact', $driver->emergency_contact) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="input textarea">{{ old('notes', $driver->notes) }}</textarea>
                    </div>
                </div>

                {{-- Right Column: License & Assignment --}}
                <div class="form-column">
                    <h2 class="card-title">License details</h2>

                    <div class="form-group">
                        <label class="form-label">License number</label>
                        <input type="text" name="license_number" id="license_number" class="input" value="{{ old('license_number', $driver->license_number) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">License type/categories</label>
                        <input type="text" name="license_type" id="license_type" class="input" value="{{ old('license_type', $driver->license_type) }}" placeholder="B, C, C+E, D...">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Issue date</label>
                            <input type="date" name="license_issue_date" id="license_issue_date" class="input" value="{{ old('license_issue_date', optional($driver->license_issue_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Expiry date</label>
                            <input type="date" name="license_expiry_date" id="license_expiry_date" class="input" value="{{ old('license_expiry_date', optional($driver->license_expiry_date)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    {{-- DVLA Verification Section --}}
                    <div class="dvla-section" style="background:#f8fafc;border-radius:10px;padding:1rem;margin-top:0.5rem;border:1px dashed #cbd5e1;">
                        <h3 style="font-size:0.85rem;font-weight:600;color:#334155;margin:0 0 0.5rem;">
                            <i class="fas fa-shield-alt" style="color:#2563eb;"></i> DVLA Verification
                        </h3>
                        <p class="field-hint" style="margin-bottom:0.75rem;">
                            Driver generates code at <a href="https://www.gov.uk/view-driving-licence" target="_blank" style="color:#2563eb;">gov.uk/view-driving-licence</a>
                        </p>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Check code</label>
                                <input type="text" id="dvla_check_code" class="input" placeholder="e.g. 8R PH KX 2D" maxlength="16">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last name</label>
                                <input type="text" id="dvla_last_name" class="input" placeholder="Surname" value="{{ explode(' ', $driver->name)[count(explode(' ', $driver->name))-1] ?? '' }}">
                            </div>
                        </div>
                        
                        <button type="button" id="dvla-verify-btn" class="btn btn-light" onclick="dvlaVerifyDriver()" style="margin-top:0.5rem;">
                            <i class="fas fa-sync-alt"></i> Verify/Refresh
                        </button>
                        <span id="dvla-status" class="field-hint" style="margin-left:0.5rem;"></span>
                    </div>

                    <h2 class="card-title" style="margin-top:1rem;">DVLA Status</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">License status</label>
                            <input type="text" name="license_status" id="license_status" class="input" value="{{ old('license_status', $driver->license_status ?? 'unknown') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Penalty points</label>
                            <input type="number" name="penalty_points" id="penalty_points" class="input" value="{{ old('penalty_points', $driver->penalty_points ?? 0) }}" min="0" max="12">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Disqualified until</label>
                        <input type="date" name="disqualified_until" id="disqualified_until" class="input" value="{{ old('disqualified_until', optional($driver->disqualified_until)->format('Y-m-d')) }}">
                        <p class="field-hint">Leave empty if not disqualified</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="input">
                            <option value="active" @selected(old('status', $driver->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $driver->status) === 'inactive')>Inactive</option>
                            <option value="on_leave" @selected(old('status', $driver->status) === 'on_leave')>On leave</option>
                        </select>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- ASSIGNMENT FIELDS                                            --}}
                    {{-- ============================================================ --}}

                    @php
                        $currentUser = auth()->user();
                        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
                        $isAdmin = $currentUser->hasRole('admin');
                        $isSuperuser = $currentUser->isSuperuser();
                    @endphp

                    {{-- Assigned Vehicles --}}
                    <div class="form-group">
                        <label class="form-label">Assigned Vehicles</label>
                        <select name="vehicle_ids[]" id="vehicle_ids" class="select2-chips" multiple="multiple" data-placeholder="Search and select vehicles...">
                            @foreach($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}" @if(in_array($vehicle->id, $driver->vehicles->pluck('id')->toArray())) selected @endif>
                                    {{ $vehicle->registration_number }}
                                    @if($vehicle->make) - {{ $vehicle->make }} {{ $vehicle->model }}@endif
                                    @if($vehicle->owner && !$isSuperuser) ({{ $vehicle->owner->name }})@endif
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Vehicles this driver can operate</p>
                    </div>

                    {{-- Employers (Superusers) - doar pentru super-admin și admin --}}
                    @if($isSuperAdmin || $isAdmin)
                    <div class="form-group">
                        <label class="form-label">Employers (Clients)</label>
                        <select name="employer_ids[]" id="employer_ids" class="select2-chips" multiple="multiple" data-placeholder="Search and select employers...">
                            @foreach($allSuperusers ?? [] as $superuser)
                                <option value="{{ $superuser->id }}" @if(in_array($superuser->id, $driver->employers->pluck('id')->toArray())) selected @endif>
                                    {{ $superuser->name }} ({{ $superuser->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Clients who employ this driver</p>
                    </div>
                    @endif

                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('management.drivers.index') }}" class="btn btn-ghost">Cancel</a>
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
    .textarea { min-height: 70px; resize: vertical; }

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
});

async function dvlaVerifyDriver() {
    const checkCode = document.getElementById('dvla_check_code').value.replace(/\s/g, '');
    const lastName = document.getElementById('dvla_last_name').value.trim();
    const licenceNumber = document.getElementById('license_number').value.trim();
    const btn = document.getElementById('dvla-verify-btn');
    const status = document.getElementById('dvla-status');
    
    if (!checkCode || checkCode.length !== 8) {
        status.textContent = 'Check code must be 8 characters';
        status.style.color = '#b91c1c';
        return;
    }
    if (!lastName) {
        status.textContent = 'Please enter driver\'s last name';
        status.style.color = '#b91c1c';
        return;
    }
    if (!licenceNumber || licenceNumber.length < 8) {
        status.textContent = 'Licence number must be at least 8 characters';
        status.style.color = '#b91c1c';
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
    status.textContent = 'Checking DVLA database...';
    status.style.color = '#6b7280';
    
    try {
        const response = await fetch('{{ route("management.dvla.driver") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                check_code: checkCode,
                last_name: lastName,
                licence_number: licenceNumber,
            }),
        });
        
        const result = await response.json();
        
        if (result.success && result.data) {
            const data = result.data;
            
            if (data.license_status) document.getElementById('license_status').value = data.license_status;
            if (data.license_type) document.getElementById('license_type').value = data.license_type;
            if (data.license_issue_date) document.getElementById('license_issue_date').value = data.license_issue_date;
            if (data.license_expiry_date) document.getElementById('license_expiry_date').value = data.license_expiry_date;
            if (data.penalty_points !== undefined) document.getElementById('penalty_points').value = data.penalty_points;
            if (data.disqualified_until) document.getElementById('disqualified_until').value = data.disqualified_until;
            
            let summary = '✓ Verified: ' + data.license_status;
            if (data.penalty_points > 0) {
                summary += ' | ' + data.penalty_points + ' points';
            }
            status.textContent = summary;
            status.style.color = '#059669';
        } else {
            status.textContent = result.error || 'Verification failed - check code may be expired';
            status.style.color = '#b91c1c';
        }
    } catch (error) {
        console.error('DVLA verify error:', error);
        status.textContent = 'Error connecting to DVLA service';
        status.style.color = '#b91c1c';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Verify/Refresh';
    }
}
</script>
@endsection
