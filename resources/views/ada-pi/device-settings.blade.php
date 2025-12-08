@extends('layouts.app')

@section('title', 'Device Settings - ' . $device->device_name)

@section('content')
<div class="page-wrapper">
    <div class="header-card">
        <div class="page-header">
            <div>
                <h1 class="page-title"><i class="fas fa-cog"></i> {{ $device->device_name }} Settings</h1>
                <p class="page-subtitle">Configure device parameters • Changes sync on next upload</p>
            </div>
            <div class="page-header-actions">
                <button type="button" class="btn btn-primary" id="saveAllBtn">
                    <i class="fas fa-save"></i>
                    <span>Save All</span>
                </button>
                <a href="{{ route('ada-pi.devices.show', $device) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <form id="settingsForm">
        @csrf
        <div class="settings-grid">
            {{-- General --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-info-circle"></i> General</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Device Name</label>
                        <input type="text" class="form-input" name="device_name" value="{{ $device->device_name }}">
                    </div>
                    <div class="form-group">
                        <label>Serial Number</label>
                        <input type="text" class="form-input" value="{{ $device->serial_number }}" disabled>
                        <small class="form-hint">Cannot be changed</small>
                    </div>
                </div>
            </div>

            {{-- Cloud --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-cloud"></i> Cloud</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Upload URL</label>
                        <input type="url" class="form-input" name="cloud_upload_url" value="{{ $settings['cloud']['upload_url'] ?? '' }}" placeholder="https://api.example.com/upload">
                    </div>
                    <div class="form-group">
                        <label>Logs URL</label>
                        <input type="url" class="form-input" name="cloud_logs_url" value="{{ $settings['cloud']['logs_url'] ?? '' }}" placeholder="https://api.example.com/logs">
                    </div>
                </div>
            </div>

            {{-- WiFi --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-wifi"></i> WiFi</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>WiFi Enabled</label>
                        <label class="toggle"><input type="checkbox" name="wifi_enabled" {{ ($settings['wifi']['enabled'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                    <div class="form-group">
                        <label>SSID</label>
                        <input type="text" class="form-input" name="wifi_ssid" value="{{ $settings['wifi']['ssid'] ?? '' }}" placeholder="Network name">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-input" name="wifi_password" value="{{ $settings['wifi']['password'] ?? '' }}" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label>IP Mode</label>
                        <select class="form-input" name="wifi_dhcp" id="wifiDhcp">
                            <option value="1" {{ ($settings['wifi']['dhcp'] ?? true) ? 'selected' : '' }}>DHCP (Automatic)</option>
                            <option value="0" {{ !($settings['wifi']['dhcp'] ?? true) ? 'selected' : '' }}>Static IP</option>
                        </select>
                    </div>
                    <div class="form-group static-ip-field">
                        <label>Static IP</label>
                        <input type="text" class="form-input" name="wifi_ip" value="{{ $settings['wifi']['ip'] ?? '' }}" placeholder="192.168.1.100">
                    </div>
                    <div class="form-group static-ip-field">
                        <label>Gateway</label>
                        <input type="text" class="form-input" name="wifi_gateway" value="{{ $settings['wifi']['gateway'] ?? '' }}" placeholder="192.168.1.1">
                    </div>
                    <div class="form-group static-ip-field">
                        <label>DNS</label>
                        <input type="text" class="form-input" name="wifi_dns" value="{{ $settings['wifi']['dns'] ?? '' }}" placeholder="8.8.8.8">
                    </div>
                </div>
            </div>

            {{-- Bluetooth --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fab fa-bluetooth-b"></i> Bluetooth</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Bluetooth Enabled</label>
                        <label class="toggle"><input type="checkbox" name="bluetooth_enabled" {{ ($settings['bluetooth']['enabled'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                    <div class="form-group">
                        <label>Discoverable</label>
                        <label class="toggle"><input type="checkbox" name="bluetooth_discoverable" {{ ($settings['bluetooth']['discoverable'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                    <div class="form-group">
                        <label>Device Name</label>
                        <input type="text" class="form-input" name="bluetooth_name" value="{{ $settings['bluetooth']['name'] ?? $device->device_name }}" placeholder="ADA-Pi-001">
                    </div>
                </div>
            </div>

            {{-- Modem --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-signal"></i> Modem / Cellular</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>APN</label>
                        <input type="text" class="form-input" name="modem_apn" value="{{ $settings['modem']['apn'] ?? '' }}" placeholder="internet">
                    </div>
                    <div class="form-group">
                        <label>APN Username</label>
                        <input type="text" class="form-input" name="modem_username" value="{{ $settings['modem']['username'] ?? '' }}" placeholder="(optional)">
                    </div>
                    <div class="form-group">
                        <label>APN Password</label>
                        <input type="password" class="form-input" name="modem_password" value="{{ $settings['modem']['password'] ?? '' }}" placeholder="(optional)">
                    </div>
                    <div class="form-group">
                        <label>Network Mode</label>
                        <select class="form-input" name="modem_network_mode">
                            <option value="auto" {{ ($settings['modem']['network_mode'] ?? 'auto') == 'auto' ? 'selected' : '' }}>Auto</option>
                            <option value="4g" {{ ($settings['modem']['network_mode'] ?? '') == '4g' ? 'selected' : '' }}>4G Only</option>
                            <option value="3g" {{ ($settings['modem']['network_mode'] ?? '') == '3g' ? 'selected' : '' }}>3G Only</option>
                            <option value="2g" {{ ($settings['modem']['network_mode'] ?? '') == '2g' ? 'selected' : '' }}>2G Only</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Roaming</label>
                        <label class="toggle"><input type="checkbox" name="modem_roaming" {{ ($settings['modem']['roaming'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                </div>
            </div>

            {{-- GPS --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-satellite"></i> GPS</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>GPS Enabled</label>
                        <label class="toggle"><input type="checkbox" name="gps_enabled" {{ ($settings['gps']['enabled'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                    <div class="form-group">
                        <label>Update Rate</label>
                        <select class="form-input" name="gps_update_rate">
                            <option value="1" {{ ($settings['gps']['update_rate'] ?? 1) == 1 ? 'selected' : '' }}>1 Hz</option>
                            <option value="2" {{ ($settings['gps']['update_rate'] ?? 1) == 2 ? 'selected' : '' }}>2 Hz</option>
                            <option value="5" {{ ($settings['gps']['update_rate'] ?? 1) == 5 ? 'selected' : '' }}>5 Hz</option>
                            <option value="10" {{ ($settings['gps']['update_rate'] ?? 1) == 10 ? 'selected' : '' }}>10 Hz</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- OBD-II --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-car"></i> OBD-II</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Port</label>
                        <select class="form-input" name="obd_port">
                            <option value="auto" {{ ($settings['obd']['port'] ?? 'auto') == 'auto' ? 'selected' : '' }}>Auto Detect</option>
                            <option value="/dev/ttyUSB0" {{ ($settings['obd']['port'] ?? '') == '/dev/ttyUSB0' ? 'selected' : '' }}>/dev/ttyUSB0</option>
                            <option value="/dev/ttyUSB1" {{ ($settings['obd']['port'] ?? '') == '/dev/ttyUSB1' ? 'selected' : '' }}>/dev/ttyUSB1</option>
                            <option value="/dev/ttyACM0" {{ ($settings['obd']['port'] ?? '') == '/dev/ttyACM0' ? 'selected' : '' }}>/dev/ttyACM0</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Protocol</label>
                        <select class="form-input" name="obd_protocol">
                            <option value="auto" {{ ($settings['obd']['protocol'] ?? 'auto') == 'auto' ? 'selected' : '' }}>Auto Detect</option>
                            <option value="6" {{ ($settings['obd']['protocol'] ?? '') == '6' ? 'selected' : '' }}>ISO 15765-4 CAN 11bit</option>
                            <option value="7" {{ ($settings['obd']['protocol'] ?? '') == '7' ? 'selected' : '' }}>ISO 15765-4 CAN 29bit</option>
                            <option value="1" {{ ($settings['obd']['protocol'] ?? '') == '1' ? 'selected' : '' }}>SAE J1850 PWM</option>
                            <option value="2" {{ ($settings['obd']['protocol'] ?? '') == '2' ? 'selected' : '' }}>SAE J1850 VPW</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Poll Interval (seconds)</label>
                        <input type="number" class="form-input" name="obd_poll_interval" value="{{ $settings['obd']['poll_interval'] ?? 2 }}" min="1" max="10">
                    </div>
                    <div class="form-group">
                        <label>Excluded Ports</label>
                        <input type="text" class="form-input" name="obd_excluded_ports" value="{{ $settings['obd']['excluded_ports'] ?? '/dev/ttyUSB2,/dev/ttyUSB3' }}" placeholder="/dev/ttyUSB2,/dev/ttyUSB3">
                        <small class="form-hint">Comma separated</small>
                    </div>
                </div>
            </div>

            {{-- UPS --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-battery-three-quarters"></i> UPS / Power</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Low Battery Threshold (%)</label>
                        <input type="number" class="form-input" name="ups_low_threshold" value="{{ $settings['ups']['low_threshold'] ?? 15 }}" min="5" max="50">
                        <small class="form-hint">Pi will shutdown below this level</small>
                    </div>
                    <div class="form-group">
                        <label>Auto Power On</label>
                        <label class="toggle"><input type="checkbox" name="ups_auto_power_on" {{ ($settings['ups']['auto_power_on'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                        <small class="form-hint">Boot when power returns</small>
                    </div>
                    <div class="form-group">
                        <label>Shutdown Delay (seconds)</label>
                        <input type="number" class="form-input" name="ups_shutdown_delay" value="{{ $settings['ups']['shutdown_delay'] ?? 30 }}" min="10" max="300">
                    </div>
                </div>
            </div>

            {{-- Fan --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-fan"></i> Fan / Cooling</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Fan Mode</label>
                        <select class="form-input" name="fan_mode" id="fanMode">
                            <option value="auto" {{ ($settings['fan']['mode'] ?? 'auto') == 'auto' ? 'selected' : '' }}>Auto (Temperature Based)</option>
                            <option value="on" {{ ($settings['fan']['mode'] ?? '') == 'on' ? 'selected' : '' }}>Always On</option>
                            <option value="off" {{ ($settings['fan']['mode'] ?? '') == 'off' ? 'selected' : '' }}>Always Off</option>
                        </select>
                    </div>
                    <div class="form-group fan-auto-field">
                        <label>Threshold Temperature (°C)</label>
                        <input type="number" class="form-input" name="fan_threshold" value="{{ $settings['fan']['threshold'] ?? 50 }}" min="30" max="80">
                    </div>
                    <div class="form-group fan-auto-field">
                        <label>Fan Speed (%)</label>
                        <input type="number" class="form-input" name="fan_speed" value="{{ $settings['fan']['speed'] ?? 100 }}" min="25" max="100" step="25">
                    </div>
                </div>
            </div>

            {{-- System --}}
            <div class="settings-card">
                <div class="card-header"><h3><i class="fas fa-microchip"></i> System</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Timezone</label>
                        <select class="form-input" name="system_timezone">
                            <option value="UTC" {{ ($settings['system']['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Europe/London" {{ ($settings['system']['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="Europe/Bucharest" {{ ($settings['system']['timezone'] ?? '') == 'Europe/Bucharest' ? 'selected' : '' }}>Europe/Bucharest</option>
                            <option value="Europe/Paris" {{ ($settings['system']['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hostname</label>
                        <input type="text" class="form-input" name="system_hostname" value="{{ $settings['system']['hostname'] ?? $device->device_name }}" placeholder="ada-pi-001">
                    </div>
                    <div class="form-group">
                        <label>Auto Update</label>
                        <label class="toggle"><input type="checkbox" name="system_auto_update" {{ ($settings['system']['auto_update'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    </div>
                    <div class="form-group">
                        <label>Daily Reboot</label>
                        <select class="form-input" name="system_reboot_schedule">
                            <option value="disabled" {{ ($settings['system']['reboot_schedule'] ?? 'disabled') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                            <option value="03:00" {{ ($settings['system']['reboot_schedule'] ?? '') == '03:00' ? 'selected' : '' }}>03:00 AM</option>
                            <option value="04:00" {{ ($settings['system']['reboot_schedule'] ?? '') == '04:00' ? 'selected' : '' }}>04:00 AM</option>
                            <option value="05:00" {{ ($settings['system']['reboot_schedule'] ?? '') == '05:00' ? 'selected' : '' }}>05:00 AM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Danger Zone</label>
                        <div class="danger-buttons">
                            <button type="button" class="btn btn-warning" id="rebootBtn"><i class="fas fa-redo"></i> Reboot</button>
                            <button type="button" class="btn btn-danger" id="factoryResetBtn"><i class="fas fa-exclamation-triangle"></i> Factory Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.page-wrapper { max-width: 1400px; margin: 0 auto; padding: 24px 16px 40px; }
.header-card { background: #fff; border-radius: 18px; padding: 16px 20px; margin-bottom: 18px; box-shadow: 0 18px 45px rgba(124,58,237,0.2), 0 0 0 1px rgba(148,163,184,0.18); }
.page-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
.page-title i { color: #8b5cf6; }
.page-subtitle { font-size: 13px; color: #64748b; margin: 4px 0 0 0; }
.page-header-actions { display: flex; gap: 10px; }
.btn { border-radius: 999px; padding: 0.5rem 1.1rem; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.15s ease; cursor: pointer; border: none; }
.btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
.btn-secondary { background: #f1f5f9; color: #475569; }
.btn-secondary:hover { background: #e2e8f0; }
.btn-warning { background: #f59e0b; color: #fff; }
.btn-warning:hover { background: #d97706; }
.btn-danger { background: #ef4444; color: #fff; }
.btn-danger:hover { background: #dc2626; }
.settings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; }
.settings-card { background: #fff; border-radius: 14px; border: 1px solid #e5e7eb; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; }
.settings-card .card-header { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; background: #fafafa; }
.settings-card .card-header h3 { font-size: 14px; font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px; }
.settings-card .card-header h3 i { color: #8b5cf6; width: 18px; text-align: center; }
.card-body { padding: 16px; }
.form-group { margin-bottom: 14px; }
.form-group:last-child { margin-bottom: 0; }
.form-group label { display: block; font-size: 12px; font-weight: 500; color: #6b7280; margin-bottom: 6px; }
.form-input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; color: #374151; transition: border-color 0.15s ease, box-shadow 0.15s ease; background: #fff; box-sizing: border-box; }
.form-input:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.1); }
.form-input:disabled { background: #f3f4f6; color: #9ca3af; }
.form-input::placeholder { color: #9ca3af; }
select.form-input { cursor: pointer; }
.form-hint { display: block; font-size: 11px; color: #9ca3af; margin-top: 4px; }
.toggle { position: relative; display: inline-block; width: 44px; height: 24px; }
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: 0.2s; border-radius: 24px; }
.toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.2s; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
.toggle input:checked + .toggle-slider { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.toggle input:checked + .toggle-slider:before { transform: translateX(20px); }
.danger-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
.danger-buttons .btn { font-size: 12px; padding: 8px 14px; }
.static-ip-field, .fan-auto-field { display: none; }
.static-ip-field.show, .fan-auto-field.show { display: block; }
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } .page-header { flex-direction: column; align-items: flex-start; } .page-header-actions { width: 100%; justify-content: flex-end; } }
.toast { position: fixed; bottom: 24px; right: 24px; background: #10b981; color: white; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transform: translateY(100px); opacity: 0; transition: all 0.3s ease; z-index: 1000; }
.toast.show { transform: translateY(0); opacity: 1; }
.toast.error { background: #ef4444; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    const saveBtn = document.getElementById('saveAllBtn');
    const wifiDhcp = document.getElementById('wifiDhcp');
    const fanMode = document.getElementById('fanMode');
    const rebootBtn = document.getElementById('rebootBtn');
    const factoryResetBtn = document.getElementById('factoryResetBtn');

    function updateStaticIpFields() {
        const show = wifiDhcp.value === '0';
        document.querySelectorAll('.static-ip-field').forEach(el => el.classList.toggle('show', show));
    }
    wifiDhcp.addEventListener('change', updateStaticIpFields);
    updateStaticIpFields();

    function updateFanFields() {
        const show = fanMode.value === 'auto';
        document.querySelectorAll('.fan-auto-field').forEach(el => el.classList.toggle('show', show));
    }
    fanMode.addEventListener('change', updateFanFields);
    updateFanFields();

    function showToast(message, isError = false) {
        let toast = document.querySelector('.toast');
        if (!toast) { toast = document.createElement('div'); toast.className = 'toast'; document.body.appendChild(toast); }
        toast.innerHTML = '<i class="fas fa-' + (isError ? 'exclamation-circle' : 'check-circle') + '"></i> ' + message;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    saveBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        fetch('{{ route("ada-pi.devices.settings.save", $device) }}', { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json())
        .then(data => { showToast(data.success ? 'Settings saved! Will sync on next upload.' : (data.message || 'Failed'), !data.success); })
        .catch(e => { showToast('Error saving settings', true); console.error(e); })
        .finally(() => { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="fas fa-save"></i> <span>Save All</span>'; });
    });

    rebootBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to reboot this device?')) {
            fetch('{{ route("ada-pi.devices.command", $device) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ command: 'reboot' }) })
            .then(r => r.json()).then(d => showToast('Reboot command queued.')).catch(e => showToast('Error sending command', true));
        }
    });

    factoryResetBtn.addEventListener('click', function() {
        if (confirm('WARNING: This will reset ALL settings. Are you sure?')) {
            const confirmation = prompt('Type RESET to confirm:');
            if (confirmation === 'RESET') {
                fetch('{{ route("ada-pi.devices.command", $device) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ command: 'factory_reset' }) })
                .then(r => r.json()).then(d => showToast('Factory reset command queued.')).catch(e => showToast('Error sending command', true));
            }
        }
    });
});
</script>
@endsection