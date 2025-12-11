@extends('layouts.app')

@section('title', 'Add vehicle')

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
        <h1 class="page-title">Add vehicle</h1>
        <p class="page-subtitle">
            Register a new vehicle and optionally assign a tracking device.
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
    <form action="{{ route('management.vehicles.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            {{-- Vehicle details --}}
            <div>
                <h2 class="form-section-title">Vehicle details</h2>

                <div class="field-group">
                    <label class="field-label">Registration number (plate)</label>
                    <div style="display:flex;gap:0.5rem;">
                        <input type="text"
                               name="registration_number"
                               id="registration_number"
                               class="input @error('registration_number') input-error @enderror"
                               value="{{ old('registration_number') }}"
                               placeholder="e.g. ADA 123"
                               style="flex:1;">
                        <button type="button" 
                                id="dvla-lookup-btn"
                                class="btn btn-secondary"
                                onclick="dvlaLookup()"
                                title="Lookup vehicle details from DVLA">
                            <i class="fas fa-search"></i>
                            <span>DVLA</span>
                        </button>
                    </div>
                    @error('registration_number')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <small class="field-help" id="dvla-status"></small>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Make</label>
                        <input type="text"
                               name="make"
                               id="make"
                               class="input @error('make') input-error @enderror"
                               value="{{ old('make') }}"
                               placeholder="e.g. Ford">
                        @error('make')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Model</label>
                        <input type="text"
                               name="model"
                               id="model"
                               class="input @error('model') input-error @enderror"
                               value="{{ old('model') }}"
                               placeholder="e.g. Transit">
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
                               id="year"
                               class="input @error('year') input-error @enderror"
                               value="{{ old('year') }}"
                               placeholder="e.g. 2024">
                        @error('year')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">VIN (optional)</label>
                        <input type="text"
                               name="vin"
                               id="vin"
                               class="input @error('vin') input-error @enderror"
                               value="{{ old('vin') }}"
                               placeholder="Vehicle identification number">
                        @error('vin')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Colour</label>
                        <input type="text"
                               name="colour"
                               id="colour"
                               class="input @error('colour') input-error @enderror"
                               value="{{ old('colour') }}"
                               placeholder="e.g. BLUE">
                        @error('colour')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Fuel type</label>
                        <input type="text"
                               name="fuel_type"
                               id="fuel_type"
                               class="input @error('fuel_type') input-error @enderror"
                               value="{{ old('fuel_type') }}"
                               placeholder="e.g. PETROL">
                        @error('fuel_type')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Engine capacity (cc)</label>
                        <input type="number"
                               name="engine_capacity"
                               id="engine_capacity"
                               class="input @error('engine_capacity') input-error @enderror"
                               value="{{ old('engine_capacity') }}"
                               placeholder="e.g. 2000">
                        @error('engine_capacity')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">CO2 emissions (g/km)</label>
                        <input type="number"
                               name="co2_emissions"
                               id="co2_emissions"
                               class="input @error('co2_emissions') input-error @enderror"
                               value="{{ old('co2_emissions') }}"
                               placeholder="e.g. 135">
                        @error('co2_emissions')
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
                        <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="service"  {{ old('status') === 'service'  ? 'selected' : '' }}>In service</option>
                    </select>
                    @error('status')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Assigned device (optional)</label>
                    <select name="device_id" class="select @error('device_id') input-error @enderror">
                        <option value="">No device</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
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
                        Only devices that are not already assigned to another vehicle are listed here.
                    </small>
                </div>

                <h2 class="form-section-title" style="margin-top:1.5rem;">MOT & Tax (from DVLA)</h2>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">MOT status</label>
                        <input type="text"
                               name="mot_status"
                               id="mot_status"
                               class="input"
                               value="{{ old('mot_status') }}"
                               placeholder="e.g. Valid"
                               readonly>
                    </div>

                    <div class="field-group">
                        <label class="field-label">MOT expiry</label>
                        <input type="date"
                               name="mot_expiry_date"
                               id="mot_expiry_date"
                               class="input"
                               value="{{ old('mot_expiry_date') }}">
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Tax status</label>
                        <input type="text"
                               name="tax_status"
                               id="tax_status"
                               class="input"
                               value="{{ old('tax_status') }}"
                               placeholder="e.g. Taxed"
                               readonly>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Tax due date</label>
                        <input type="date"
                               name="tax_due_date"
                               id="tax_due_date"
                               class="input"
                               value="{{ old('tax_due_date') }}">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Euro status</label>
                    <input type="text"
                           name="euro_status"
                           id="euro_status"
                           class="input"
                           value="{{ old('euro_status') }}"
                           placeholder="e.g. EURO 6"
                           readonly>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('management.vehicles.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Create vehicle</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
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
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Looking up...</span>';
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
            // Auto-fill the form fields
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
            
            status.textContent = '✓ Vehicle found! Fields auto-filled.' + (result.sandbox ? ' (Sandbox mode)' : '');
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
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-search"></i> <span>DVLA</span>';
    }
}

// Auto-lookup when user leaves registration field (optional)
document.getElementById('registration_number').addEventListener('blur', function() {
    // Only auto-lookup if make field is empty (not already filled)
    if (this.value.trim() && !document.getElementById('make').value) {
        // dvlaLookup(); // Uncomment to enable auto-lookup on blur
    }
});
</script>
@endsection
