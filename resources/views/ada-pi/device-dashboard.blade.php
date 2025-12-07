@extends('layouts.app')

@section('title', $device->device_name . ' - Device Dashboard')

@section('content')
<div class="page-wrapper">
    {{-- Header --}}
    <div class="header-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">{{ $device->device_name }}</h1>
                <div class="page-meta">
                    @if($device->vehicle)
                        <span class="badge badge-vehicle">{{ $device->vehicle->registration_number }}</span>
                    @endif
                    <span id="statusBadge" class="badge {{ $device->isOnline() ? 'badge-success' : 'badge-danger' }}">
                        {{ $device->isOnline() ? 'ONLINE' : 'OFFLINE' }}
                    </span>
                    <span class="last-seen" id="lastSeen">
                        Last seen: {{ $device->last_online?->diffForHumans() ?? 'Never' }}
                    </span>
                </div>
            </div>
            
            {{-- Upload Interval Slider --}}
            <div class="interval-control">
                <label class="interval-label">
                    <i class="fas fa-clock"></i>
                    Upload every
                </label>
                <div class="slider-container">
                    <input type="range" id="uploadInterval" class="interval-slider" 
                           min="5" max="60" step="5" value="{{ $device->upload_interval ?? 15 }}">
                    <span id="intervalValue" class="interval-value">{{ $device->upload_interval ?? 15 }}s</span>
                </div>
                <span id="intervalSaved" class="interval-saved">
                    <i class="fas fa-check"></i>
                </span>
            </div>
            
            <div class="page-header-actions">
                <span class="update-indicator" id="updateIndicator">
                    <span class="pulse"></span>
                    Live
                </span>
                <a href="{{ route('pi.dashboard') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Devices</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-road"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['distance'] }} km</div>
                <div class="stat-label">Distance (24h)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tachometer-alt"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['max_speed'] ?? 0 }} km/h</div>
                <div class="stat-label">Max Speed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gas-pump"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="fuelLevel">{{ $stats['avg_fuel'] ?? '--' }}%</div>
                <div class="stat-label">Avg Fuel</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-satellite"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_records'] }}</div>
                <div class="stat-label">Records (24h)</div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="dash-grid">
        {{-- Map --}}
        <div class="card card-map">
            <div class="card-header">
                <h3><i class="fas fa-map-marker-alt"></i> Location</h3>
                <div class="gps-coords" id="gpsCoords">
                    @if($latestTelemetry && $latestTelemetry->hasGps())
                        {{ number_format($latestTelemetry->latitude, 5) }}, {{ number_format($latestTelemetry->longitude, 5) }}
                    @else
                        No GPS data
                    @endif
                </div>
            </div>
            <div id="map" class="map-container"></div>
        </div>

        {{-- GPS Panel --}}
        <div class="card card-panel">
            <div class="card-header">
                <h3><i class="fas fa-satellite"></i> GPS</h3>
            </div>
            <div class="panel-grid">
                <div class="panel-item">
                    <span class="panel-label">Speed</span>
                    <span class="panel-value" id="gpsSpeed">{{ $latestTelemetry->speed ?? '--' }} km/h</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Heading</span>
                    <span class="panel-value" id="gpsHeading">{{ $latestTelemetry->heading ?? '--' }}°</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Altitude</span>
                    <span class="panel-value" id="gpsAltitude">{{ $latestTelemetry->altitude ?? '--' }} m</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Satellites</span>
                    <span class="panel-value" id="gpsSatellites">{{ $latestTelemetry->satellites ?? '--' }}</span>
                </div>
            </div>
        </div>

        {{-- OBD Panel --}}
        <div class="card card-panel">
            <div class="card-header">
                <h3><i class="fas fa-car"></i> OBD-II</h3>
            </div>
            <div class="panel-grid">
                <div class="panel-item">
                    <span class="panel-label">RPM</span>
                    <span class="panel-value" id="obdRpm">{{ $latestTelemetry->rpm ?? '--' }}</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Speed</span>
                    <span class="panel-value" id="obdSpeed">{{ $latestTelemetry->vehicle_speed ?? '--' }} km/h</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Coolant</span>
                    <span class="panel-value" id="obdCoolant">{{ $latestTelemetry->coolant_temp ?? '--' }}°C</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Fuel</span>
                    <span class="panel-value" id="obdFuel">{{ $latestTelemetry->fuel_level ?? '--' }}%</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Throttle</span>
                    <span class="panel-value" id="obdThrottle">{{ $latestTelemetry->throttle ?? '--' }}%</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Engine Load</span>
                    <span class="panel-value" id="obdLoad">{{ $latestTelemetry->engine_load ?? '--' }}%</span>
                </div>
            </div>
        </div>

        {{-- Modem Panel --}}
        <div class="card card-panel">
            <div class="card-header">
                <h3><i class="fas fa-signal"></i> Modem</h3>
            </div>
            <div class="panel-grid">
                <div class="panel-item">
                    <span class="panel-label">Signal</span>
                    <span class="panel-value" id="modemSignal">{{ $latestTelemetry->signal_strength ?? '--' }} dBm</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Network</span>
                    <span class="panel-value" id="modemNetwork">{{ $latestTelemetry->network_type ?? '--' }}</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Operator</span>
                    <span class="panel-value" id="modemOperator">{{ $latestTelemetry->operator ?? '--' }}</span>
                </div>
            </div>
        </div>

        {{-- System Panel --}}
        <div class="card card-panel">
            <div class="card-header">
                <h3><i class="fas fa-microchip"></i> System</h3>
            </div>
            <div class="panel-grid">
                <div class="panel-item">
                    <span class="panel-label">CPU Temp</span>
                    <span class="panel-value" id="sysCpuTemp">{{ $latestTelemetry->cpu_temp ?? '--' }}°C</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">CPU Usage</span>
                    <span class="panel-value" id="sysCpu">{{ $latestTelemetry->cpu_usage ?? '--' }}%</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Memory</span>
                    <span class="panel-value" id="sysMemory">{{ $latestTelemetry->memory_usage ?? '--' }}%</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Disk</span>
                    <span class="panel-value" id="sysDisk">{{ $latestTelemetry->disk_usage ?? '--' }}%</span>
                </div>
            </div>
        </div>

        {{-- UPS Panel --}}
        <div class="card card-panel">
            <div class="card-header">
                <h3><i class="fas fa-battery-three-quarters"></i> UPS</h3>
            </div>
            <div class="panel-grid">
                <div class="panel-item">
                    <span class="panel-label">Battery</span>
                    <span class="panel-value" id="upsBattery">{{ $latestTelemetry->battery_percent ?? '--' }}%</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Voltage</span>
                    <span class="panel-value" id="upsVoltage">{{ $latestTelemetry->battery_voltage ?? '--' }}V</span>
                </div>
                <div class="panel-item">
                    <span class="panel-label">Charging</span>
                    <span class="panel-value" id="upsCharging">
                        @if($latestTelemetry && $latestTelemetry->is_charging !== null)
                            {{ $latestTelemetry->is_charging ? 'Yes' : 'No' }}
                        @else
                            --
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    .page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 16px 40px;
    }

    .header-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px 20px;
        margin-bottom: 18px;
        box-shadow: 0 18px 45px rgba(124, 58, 237, 0.2), 0 0 0 1px rgba(148, 163, 184, 0.18);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .page-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 6px;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Interval Slider */
    .interval-control {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .interval-label {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .interval-label i {
        color: #8b5cf6;
    }

    .slider-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .interval-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 120px;
        height: 6px;
        background: linear-gradient(to right, #8b5cf6 0%, #8b5cf6 var(--progress, 17%), #e2e8f0 var(--progress, 17%), #e2e8f0 100%);
        border-radius: 3px;
        outline: none;
        cursor: pointer;
    }

    .interval-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        background: #8b5cf6;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(139, 92, 246, 0.4);
        transition: transform 0.15s ease;
    }

    .interval-slider::-webkit-slider-thumb:hover {
        transform: scale(1.1);
    }

    .interval-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: #8b5cf6;
        border-radius: 50%;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 6px rgba(139, 92, 246, 0.4);
    }

    .interval-value {
        font-size: 14px;
        font-weight: 600;
        color: #8b5cf6;
        min-width: 35px;
    }

    .interval-saved {
        color: #10b981;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .interval-saved.show {
        opacity: 1;
    }

    .last-seen {
        font-size: 12px;
        color: #6b7280;
    }

    .update-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 500;
        color: #10b981;
    }

    .pulse {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* Buttons */
    .btn {
        border-radius: 999px;
        padding: 0.5rem 1.1rem;
        border: 1px solid transparent;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .btn i {
        font-size: 0.9rem;
    }

    .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-icon {
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
    }

    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .stat-label {
        font-size: 12px;
        color: #6b7280;
    }

    .dash-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 16px;
    }

    .card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .card-header {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-header h3 i {
        color: #6366f1;
    }

    .gps-coords {
        font-size: 11px;
        color: #9ca3af;
        font-family: monospace;
    }

    .card-map {
        grid-row: span 2;
    }

    .map-container {
        height: 400px;
        background: #f3f4f6;
    }

    .card-panel {
        padding-bottom: 0;
    }

    .panel-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1px;
        background: #f3f4f6;
    }

    .panel-item {
        background: #fff;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .panel-label {
        font-size: 11px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .panel-value {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    .badge {
        display: inline-flex;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-vehicle {
        background: #eef2ff;
        color: #4338ca;
    }

    .btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-ghost {
        background: transparent;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-ghost:hover {
        background: #f9fafb;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .dash-grid {
            grid-template-columns: 1fr 1fr;
        }
        .card-map {
            grid-column: span 2;
            grid-row: span 1;
        }
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .dash-grid {
            grid-template-columns: 1fr;
        }
        .card-map {
            grid-column: span 1;
        }
        .stats-row {
            grid-template-columns: 1fr;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .page-header-actions {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endsection

@section('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inițializare hartă
    const gpsHistory = @json($gpsHistory);
    const defaultLat = {{ $latestTelemetry->latitude ?? 52.4862 }};
    const defaultLng = {{ $latestTelemetry->longitude ?? -1.8904 }};

    const map = L.map('map').setView([defaultLat, defaultLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Marker pentru poziția curentă
    let marker = L.marker([defaultLat, defaultLng]).addTo(map);

    // Traseu GPS
    if (gpsHistory.length > 1) {
        const coords = gpsHistory.map(p => [p.latitude, p.longitude]);
        L.polyline(coords, { color: '#6366f1', weight: 3, opacity: 0.7 }).addTo(map);
        map.fitBounds(coords);
    }

    // Live updates
    const deviceId = {{ $device->id }};
    
    function updateData() {
        fetch(`/ada-pi/devices/${deviceId}/live`)
            .then(r => r.json())
            .then(data => {
                // Status
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.textContent = data.online ? 'ONLINE' : 'OFFLINE';
                statusBadge.className = 'badge ' + (data.online ? 'badge-success' : 'badge-danger');
                
                document.getElementById('lastSeen').textContent = 'Last seen: ' + (data.last_online || 'Never');

                if (data.telemetry) {
                    const t = data.telemetry;

                    // GPS
                    if (t.gps.lat && t.gps.lng) {
                        marker.setLatLng([t.gps.lat, t.gps.lng]);
                        map.panTo([t.gps.lat, t.gps.lng]);
                        document.getElementById('gpsCoords').textContent = 
                            t.gps.lat.toFixed(5) + ', ' + t.gps.lng.toFixed(5);
                    }
                    document.getElementById('gpsSpeed').textContent = (t.gps.speed ?? '--') + ' km/h';
                    document.getElementById('gpsSatellites').textContent = t.gps.satellites ?? '--';

                    // OBD
                    document.getElementById('obdRpm').textContent = t.obd.rpm ?? '--';
                    document.getElementById('obdSpeed').textContent = (t.obd.speed ?? '--') + ' km/h';
                    document.getElementById('obdCoolant').textContent = (t.obd.coolant ?? '--') + '°C';
                    document.getElementById('obdFuel').textContent = (t.obd.fuel ?? '--') + '%';

                    // Modem
                    document.getElementById('modemSignal').textContent = (t.modem.signal ?? '--') + ' dBm';
                    document.getElementById('modemNetwork').textContent = t.modem.network ?? '--';
                    document.getElementById('modemOperator').textContent = t.modem.operator ?? '--';

                    // UPS
                    document.getElementById('upsBattery').textContent = (t.ups.battery ?? '--') + '%';

                    // System
                    document.getElementById('sysCpuTemp').textContent = (t.system.cpu_temp ?? '--') + '°C';
                    document.getElementById('sysCpu').textContent = (t.system.cpu ?? '--') + '%';
                    document.getElementById('sysMemory').textContent = (t.system.memory ?? '--') + '%';
                }
            })
            .catch(err => console.error('Update failed:', err));
    }

    // Update every 10 seconds
    setInterval(updateData, 10000);

    // ============================================
    // Upload Interval Slider
    // ============================================
    const intervalSlider = document.getElementById('uploadInterval');
    const intervalValue = document.getElementById('intervalValue');
    const intervalSaved = document.getElementById('intervalSaved');
    let saveTimeout = null;

    // Update slider visual progress
    function updateSliderProgress() {
        const min = parseInt(intervalSlider.min);
        const max = parseInt(intervalSlider.max);
        const val = parseInt(intervalSlider.value);
        const progress = ((val - min) / (max - min)) * 100;
        intervalSlider.style.setProperty('--progress', progress + '%');
    }

    // Initial progress
    updateSliderProgress();

    intervalSlider.addEventListener('input', function() {
        intervalValue.textContent = this.value + 's';
        updateSliderProgress();
        
        // Debounce save
        clearTimeout(saveTimeout);
        intervalSaved.classList.remove('show');
        
        saveTimeout = setTimeout(() => {
            saveInterval(this.value);
        }, 500);
    });

    function saveInterval(value) {
        fetch(`/ada-pi/devices/${deviceId}/interval`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ interval: value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                intervalSaved.classList.add('show');
                setTimeout(() => intervalSaved.classList.remove('show'), 2000);
            }
        })
        .catch(err => console.error('Failed to save interval:', err));
    }
});
</script>
@endsection
