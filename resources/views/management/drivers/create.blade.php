@extends('layouts.app')

@section('title', 'Add driver')

@section('styles')
<style>
.form-card {
    max-width: 980px;
    margin: 0 auto;
}
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
.btn-primary:hover { background:#1d4ed8; transform:translateY(-1px); }
.btn-secondary {
    background:#fff;
    color:#111827;
    border-color:#e5e7eb;
}
.btn-secondary:hover { background:#f9fafb; }
.btn-ghost {
    background:transparent;
    color:#6b7280;
}

/* Form layout */
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
.field-group { margin-bottom:0.9rem; }
.field-label {
    display:block;
    font-size:0.8rem;
    font-weight:600;
    color:#4b5563;
    margin-bottom:0.2rem;
}
.field-help {
    font-size:0.75rem;
    color:#6b7280;
}
.field-grid-2 {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:0.75rem;
}
.input,
.select,
.textarea,
.multiselect {
    width:100%;
    border-radius:10px;
    border:1px solid #e5e7eb;
    padding:0.55rem 0.75rem;
    font-size:0.88rem;
}
.textarea {
    min-height:70px;
    resize:vertical;
}
.multiselect {
    min-height:120px;
}
.input-error {
    border-color:#f97373;
}
.field-error {
    font-size:0.75rem;
    color:#b91c1c;
    margin-top:0.15rem;
}
.required {
    color: #dc2626;
}
.form-footer {
    margin-top:1.2rem;
    display:flex;
    justify-content:flex-end;
    gap:0.75rem;
}

@media (max-width:900px) {
    .form-grid { grid-template-columns:1fr; }
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Add driver</h1>
        <p class="page-subtitle">
            Create a new driver profile, add license details and assign vehicles.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('management.drivers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to drivers</span>
        </a>
        <a href="{{ route('management.index') }}" class="btn btn-ghost">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </div>
</div>

<div class="card form-card">
    <form action="{{ route('management.drivers.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            {{-- Driver details --}}
            <div>
                <h2 class="form-section-title">Driver details</h2>

                <div class="field-group">
                    <label class="field-label">Name <span class="required">*</span></label>
                    <input type="text"
                           name="name"
                           class="input @error('name') input-error @enderror"
                           value="{{ old('name') }}"
                           placeholder="Full name"
                           required>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Email</label>
                        <input type="email"
                               name="email"
                               class="input @error('email') input-error @enderror"
                               value="{{ old('email') }}"
                               placeholder="email@example.com">
                        <p class="field-help">If provided, a login account will be created (password = license number)</p>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Phone</label>
                        <input type="text"
                               name="phone"
                               class="input @error('phone') input-error @enderror"
                               value="{{ old('phone') }}"
                               placeholder="+40 7xx xxx xxx">
                        @error('phone')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">Date of birth</label>
                        <input type="date"
                               name="date_of_birth"
                               class="input @error('date_of_birth') input-error @enderror"
                               value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">Hire date</label>
                        <input type="date"
                               name="hire_date"
                               class="input @error('hire_date') input-error @enderror"
                               value="{{ old('hire_date') }}">
                        @error('hire_date')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Address</label>
                    <textarea name="address"
                              class="textarea @error('address') input-error @enderror"
                              placeholder="Street, city, ZIP, country">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Emergency contact (optional)</label>
                    <input type="text"
                           name="emergency_contact"
                           class="input @error('emergency_contact') input-error @enderror"
                           value="{{ old('emergency_contact') }}"
                           placeholder="Name and phone number">
                    @error('emergency_contact')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Notes (optional)</label>
                    <textarea name="notes"
                              class="textarea @error('notes') input-error @enderror"
                              placeholder="Additional notes, restrictions, comments">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- License & assignment --}}
            <div>
                <h2 class="form-section-title">License &amp; assignment</h2>

                <div class="field-group">
                    <label class="field-label">License number <span class="required">*</span></label>
                    <input type="text"
                           name="license_number"
                           class="input @error('license_number') input-error @enderror"
                           value="{{ old('license_number') }}"
                           placeholder="e.g. B1234567"
                           required>
                    <p class="field-help">Also used as initial login password</p>
                    @error('license_number')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">License type <span class="required">*</span></label>
                    <input type="text"
                           name="license_type"
                           class="input @error('license_type') input-error @enderror"
                           value="{{ old('license_type') }}"
                           placeholder="e.g. B, C, CE"
                           required>
                    @error('license_type')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-grid-2">
                    <div class="field-group">
                        <label class="field-label">License issue date</label>
                        <input type="date"
                               name="license_issue_date"
                               class="input @error('license_issue_date') input-error @enderror"
                               value="{{ old('license_issue_date') }}">
                        @error('license_issue_date')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label class="field-label">License expiry date</label>
                        <input type="date"
                               name="license_expiry_date"
                               class="input @error('license_expiry_date') input-error @enderror"
                               value="{{ old('license_expiry_date') }}">
                        @error('license_expiry_date')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Status</label>
                    <select name="status" class="select @error('status') input-error @enderror">
                        @php $status = old('status', 'active'); @endphp
                        <option value="active"   {{ $status === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ $status === 'on_leave' ? 'selected' : '' }}>On leave</option>
                    </select>
                    @error('status')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Assigned vehicles (optional)</label>
                    <select name="vehicle_ids[]"
                            class="multiselect @error('vehicle_ids') input-error @enderror"
                            multiple>
                        @php
                            $selectedVehicles = old('vehicle_ids', []);
                        @endphp
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                {{ in_array($vehicle->id, $selectedVehicles) ? 'selected' : '' }}>
                                {{ $vehicle->registration_number ?? 'No plate' }}
                                @if($vehicle->make || $vehicle->model)
                                    — {{ trim(($vehicle->make ?? '').' '.($vehicle->model ?? '')) ?: 'No make / model' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_ids')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <p class="field-help">
                        Hold <strong>Ctrl</strong> (Windows) or <strong>Cmd</strong> (Mac) to select multiple vehicles.
                    </p>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('management.drivers.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Create driver</button>
        </div>
    </form>
</div>
@endsection
