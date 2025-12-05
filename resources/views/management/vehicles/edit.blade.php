@extends('dashboard')

@section('title', 'Edit vehicle')

@section('styles')
<style>
/* Header */
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
.btn-primary:hover {
    background:#1d4ed8;
    transform:translateY(-1px);
}

.btn-secondary {
    background:#fff;
    color:#111827;
    border-color:#e5e7eb;
}
.btn-secondary:hover {
    background:#f9fafb;
}

.btn-ghost {
    background:transparent;
    color:#6b7280;
}
.btn-ghost:hover {
    background:#f3f4f6;
}

.btn-icon {
    padding-inline:0.9rem;
}

/* Card + form layout */
.card {
    background:#fff;
    border-radius:18px;
    box-shadow:0 18px 55px rgba(129,140,248,0.3);
    padding:1.25rem 1.5rem;
}

.form-card {
    max-width:980px;
    margin:0 auto;
}

.form-grid {
    display:grid;
    grid-template-columns:minmax(0,1.6fr) minmax(0,1.4fr);
    gap:1.5rem;
}

.form-section-title {
    font-size:0.95rem;
    font-weight:600;
    color:#111827;
    margin-bottom:0.75rem;
}

/* Fields */
.field-group {
    margin-bottom:0.9rem;
}
.field-label {
    display:block;
    font-size:0.8rem;
    font-weight:600;
    color:#4b5563;
    margin-bottom:0.2rem;
}
.field-grid-2 {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:0.75rem;
}

.input,
.select {
    width:100%;
    border-radius:10px;
    border:1px solid #e5e7eb;
    padding:0.55rem 0.75rem;
    font-size:0.88rem;
}
.input:focus,
.select:focus {
    outline:none;
    border-color:#6366f1;
    box-shadow:0 0 0 1px rgba(99,102,241,0.25);
}

.input-error {
    border-color:#f97373;
}

.field-error {
    font-size:0.75rem;
    color:#b91c1c;
    margin-top:0.15rem;
}

.field-help {
    font-size:0.75rem;
    color:#6b7280;
}

/* Footer */
.form-footer {
    margin-top:1.2rem;
    display:flex;
    justify-content:flex-end;
    gap:0.75rem;
}

@media (max-width:900px) {
    .form-grid {
        grid-template-columns:1fr;
    }
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit vehicle</h1>
        <p class="page-subtitle">
            {{ $vehicle->registration_number ?? 'Vehicle' }}
            @if($vehicle->make || $vehicle->model)
                — {{ trim(($vehicle->make ?? '').' '.($vehicle->model ?? '')) }}
            @endif
        </p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('management.vehicles.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
            <span>Back to vehicles</span>
        </a>
        <a href="{{ route('management.index') }}" class="btn btn-ghost btn-icon">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </div>
</div>

<div class="card form-card">
    <form action="{{ route('management.vehicles.update', $vehicle) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            {{-- Vehicle details --}}
            <div>
                <h2 class="form-section-title">Vehicle details</h2>

                <div class="field-group">
                    <label class="field-label">Registration number (plate)</label>
                    <input type="text"
                           name="registration_number"
                           class="input @error('registration_number') input-error @enderror"
                           value="{{ old('registration_number', $vehicle->registration_number) }}">
                    @error('registration_number')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Make</label>
                        <input type="text"
                               name="make"
                               class="input @error('make') input-error @enderror"
                               value="{{ old('make', $vehicle->make) }}">
                        @error('make')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Model</label>
                        <input type="text"
                               name="model"
                               class="input @error('model') input-error @enderror"
                               value="{{ old('model', $vehicle->model) }}">
                        @error('model')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Year</label>
                        <input type="number"
                               name="year"
                               class="input @error('year') input-error @enderror"
                               value="{{ old('year', $vehicle->year) }}">
                        @error('year')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">VIN</label>
                        <input type="text"
                               name="vin"
                               class="input @error('vin') input-error @enderror"
                               value="{{ old('vin', $vehicle->vin) }}">
                        @error('vin')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Status & assignment --}}
            <div>
                <h2 class="form-section-title">Status & assignment</h2>

                <div class="field-group">
                    <label class="field-label">Status</label>
                    <select name="status" class="select @error('status') input-error @enderror">
                        @php $currentStatus = old('status', $vehicle->status); @endphp
                        <option value="active"   {{ $currentStatus === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $currentStatus === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="service"  {{ $currentStatus === 'service'  ? 'selected' : '' }}>In service</option>
                    </select>
                    @error('status')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Assigned device</label>
                    <select name="device_id" class="select @error('device_id') input-error @enderror">
                        <option value="">No device</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}"
                                {{ (string) old('device_id', $vehicle->device_id) === (string) $device->id ? 'selected' : '' }}>
                                {{ $device->device_name ?? 'Device' }}
                                @if($device->serial_number)
                                    — {{ $device->serial_number }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('device_id')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <small class="field-help">
                        Only free devices or the device already assigned to this vehicle are available.
                    </small>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('management.vehicles.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</div>
@endsection
