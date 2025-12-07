@extends('layouts.app')

@section('title', 'Pi Dashboard')

@section('content')
<div class="page-wrapper">
    {{-- Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <a href="{{ route('hub') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Hub</span>
            </a>
            <div>
                <h1 class="page-title">ADA-Pi Devices</h1>
            </div>
        </div>

        <div class="page-header-actions">
            {{-- Search --}}
            <form class="devices-search-form" onsubmit="return false;">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        type="text"
                        id="piDevicesSearch"
                        class="input input-search"
                        placeholder="Search devices, vehicles or super users..."
                        autocomplete="off"
                    >
                </div>
            </form>
        </div>
    </div>

    {{-- Devices grid --}}
    @if($devices->count() > 0)
        <div id="piDevicesGrid" class="pi-devices-grid">
            @foreach($devices as $device)
                @php
                    $status = $device->status ?? 'inactive';

                    // Relații
                    $vehicle     = $device->vehicle ?? null;
                    $deviceOwner = $device->owner ?? null;

                    // Owner de pe vehicul (dacă modelul are relația owner())
                    $vehicleOwner = null;
                    if ($vehicle && method_exists($vehicle, 'owner')) {
                        $vehicleOwner = $vehicle->owner;
                    }

                    // determinăm superuser-ul client
                    $client = null;
                    if ($vehicleOwner && method_exists($vehicleOwner, 'hasRole') && $vehicleOwner->hasRole('superuser')) {
                        $client = $vehicleOwner;
                    } elseif ($deviceOwner && method_exists($deviceOwner, 'hasRole') && $deviceOwner->hasRole('superuser')) {
                        $client = $deviceOwner;
                    }

                    $clientName = $client ? $client->name : null;

                    // eticheta de asignare: număr de înmatriculare sau UNASSIGNED
                    $assignmentLabel = 'UNASSIGNED';
                    if ($vehicle && !empty($vehicle->registration_number)) {
                        $assignmentLabel = $vehicle->registration_number;
                    }

                    // text pentru search
                    $searchTextParts = [
                        strtolower($device->device_name ?? ''),
                        strtolower($assignmentLabel),
                        strtolower($clientName ?? ''),
                        strtolower($status ?? ''),
                    ];
                    $searchText = trim(implode(' ', array_filter($searchTextParts)));
                @endphp

                <div class="card pi-device-card"
                     data-search="{{ $searchText }}">
                    <div class="pi-device-card-body">
                        <div class="pi-device-card-header">
                            <div class="pi-device-main">
                                <div class="pi-device-name">
                                    {{ $device->device_name ?? 'Unnamed device' }}
                                </div>

                                <div class="pi-device-meta">
                                    @if($assignmentLabel === 'UNASSIGNED')
                                        <span class="badge badge-muted">{{ $assignmentLabel }}</span>
                                    @else
                                        <span class="badge badge-vehicle">{{ $assignmentLabel }}</span>
                                    @endif

                                    @if($clientName)
                                        <span class="pi-device-owner">
                                            Super user: {{ $clientName }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="pi-device-status">
                                @if($status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($status === 'inactive')
                                    <span class="badge badge-muted">Inactive</span>
                                @else
                                    @if(method_exists($device, 'isOnline') && $device->isOnline())
                                        <span class="badge badge-success">ONLINE</span>
                                    @else
                                        <span class="badge badge-danger">OFFLINE</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="pi-device-info">
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value">{{ ucfirst($status) }}</span>
                            </div>

                            @if($device->last_online)
                                <div class="info-row">
                                    <span class="info-label">Last seen:</span>
                                    <span class="info-value">{{ $device->last_online->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pi-device-actions">
                            @if($status === 'pending')
                                {{-- Admins: Accept / Refuse --}}
                                @if(auth()->user()->hasRole(['super-admin', 'admin']))
                                    <form action="{{ route('ada-pi.devices.accept', $device) }}"
                                          method="POST"
                                          class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Accept
                                        </button>
                                    </form>

                                    <form action="{{ route('ada-pi.devices.refuse', $device) }}"
                                          method="POST"
                                          class="inline-form"
                                          onsubmit="return confirm('Mark this device as inactive?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Refuse
                                        </button>
                                    </form>
                                @else
                                    <span class="pi-device-note">
                                        Pending approval by administrator.
                                    </span>
                                @endif

                            @elseif($status === 'active')
                                {{-- Access device dashboard --}}
                                <a href="{{ route('ada-pi.devices.show', $device) }}"
                                   class="btn btn-primary btn-sm w-100">
                                    View Dashboard →
                                </a>

                            @else {{-- inactive --}}
                                <span class="pi-device-note">
                                    Device is inactive. Contact support to reactivate.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="devices-empty">
                <p>No ADA-Pi devices available.</p>
            </div>
        </div>
    @endif
</div>

<style>
    .page-wrapper {
        max-width: 1200px;
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

    .page-header-left {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        text-decoration: none;
        padding: 6px 0;
        transition: color 0.15s ease;
    }

    .back-link:hover {
        color: #4f46e5;
    }

    .back-link i {
        font-size: 11px;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    .devices-search-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-input-wrapper {
        position: relative;
        width: 260px;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 13px;
        color: #9ca3af;
    }

    .input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        padding: 8px 12px;
        padding-left: 30px;
        font-size: 13px;
        color: #111827;
        background: #f9fafb;
        transition: all 0.15s ease;
    }

    .input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.25);
        outline: none;
        background: #ffffff;
    }

    .input-search {
        min-width: 220px;
    }

    .btn {
        border-radius: 999px;
        padding: 8px 14px;
        border: 1px solid transparent;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 999px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(79, 70, 229, 0.45);
    }

    .btn-danger {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .btn-danger:hover {
        background: #fecaca;
    }

    .btn-ghost {
        background: transparent;
        border-color: #e5e7eb;
        color: #374151;
    }

    .btn-ghost:hover {
        background: #f9fafb;
    }

    .w-100 {
        width: 100%;
    }

    .card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow:
            0 18px 45px rgba(124, 58, 237, 0.18),
            0 0 0 1px rgba(148, 163, 184, 0.16);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .pi-devices-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .pi-device-card-body {
        padding: 16px 16px 14px;
    }

    .pi-device-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 10px;
    }

    .pi-device-main {
        min-width: 0;
    }

    .pi-device-name {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .pi-device-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        font-size: 12px;
        color: #6b7280;
    }

    .pi-device-owner {
        font-size: 12px;
        color: #6b7280;
    }

    .pi-device-status {
        white-space: nowrap;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 9px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .badge-vehicle {
        background: #eef2ff;
        border-color: #e0e7ff;
        color: #4338ca;
    }

    .badge-success {
        background: #ecfdf3;
        border-color: #bbf7d0;
        color: #166534;
    }

    .badge-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }

    .badge-muted {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #4b5563;
    }

    .badge-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .pi-device-info {
        margin-bottom: 10px;
        border-top: 1px dashed #e5e7eb;
        padding-top: 8px;
        font-size: 12px;
        color: #4b5563;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }

    .info-label {
        color: #6b7280;
    }

    .info-value {
        font-weight: 500;
        text-align: right;
    }

    .pi-device-actions {
        border-top: 1px solid #f3f4f6;
        padding-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-start;
    }

    .inline-form {
        display: inline-block;
    }

    .pi-device-note {
        font-size: 12px;
        color: #6b7280;
    }

    .devices-empty {
        padding: 32px 20px;
        text-align: center;
        font-size: 14px;
        color: #6b7280;
    }

    @media (max-width: 1200px) {
        .pi-devices-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .pi-devices-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .page-header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .devices-search-form {
            flex: 1;
        }
    }

    @media (max-width: 640px) {
        .pi-devices-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .page-wrapper {
            padding: 16px 12px 28px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('piDevicesSearch');
        const cards = document.querySelectorAll('#piDevicesGrid .pi-device-card');

        if (!searchInput || !cards.length) return;

        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();

            cards.forEach(card => {
                const haystack = card.dataset.search || '';
                const visible = !term || haystack.includes(term);
                card.style.display = visible ? '' : 'none';
            });
        });
    });
</script>
@endsection
