<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdaPiWebDeviceController extends Controller
{
    /**
     * Afișează Device Dashboard cu telemetrie live
     */
    public function show(Device $device)
    {
        // Verifică dacă user-ul are acces la acest device
        $user = auth()->user();
        
        if (!$user->hasRole(['super-admin', 'admin'])) {
            // Superuser/client poate vedea doar device-urile asociate vehiculelor lui
            $hasAccess = $device->vehicle && $device->vehicle->created_by === $user->id;
            if (!$hasAccess && $device->owner_id !== $user->id) {
                abort(403, 'Nu ai acces la acest device.');
            }
        }

        // Ultima telemetrie
        $latestTelemetry = $device->latestTelemetry;

        // Istoric GPS pentru hartă (ultimele 100 puncte)
        $gpsHistory = DeviceTelemetry::where('device_id', $device->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('recorded_at', 'desc')
            ->limit(100)
            ->get(['latitude', 'longitude', 'speed', 'recorded_at'])
            ->reverse()
            ->values();

        // Statistici ultimele 24h
        $stats = $this->getDeviceStats($device);

        return view('ada-pi.device-dashboard', compact(
            'device',
            'latestTelemetry',
            'gpsHistory',
            'stats'
        ));
    }

    /**
     * API endpoint pentru live updates (polling)
     */
    public function liveData(Device $device)
    {
        $latestTelemetry = $device->latestTelemetry;

        return response()->json([
            'online' => $device->isOnline(),
            'last_online' => $device->last_online?->diffForHumans(),
            'dtc' => [
                'codes' => $device->dtc_codes ?? [],
                'updated_at' => $device->dtc_updated_at?->diffForHumans(),
                'pending_command' => $device->pending_command,
            ],
            'telemetry' => $latestTelemetry ? [
                'recorded_at' => $latestTelemetry->recorded_at->format('H:i:s'),
                'gps' => [
                    'lat' => $latestTelemetry->latitude,
                    'lng' => $latestTelemetry->longitude,
                    'speed' => $latestTelemetry->speed,
                    'satellites' => $latestTelemetry->satellites,
                ],
                'obd' => [
                    'rpm' => $latestTelemetry->rpm,
                    'speed' => $latestTelemetry->vehicle_speed,
                    'coolant' => $latestTelemetry->coolant_temp,
                    'fuel' => $latestTelemetry->fuel_level,
                    'throttle' => $latestTelemetry->throttle,
                    'load' => $latestTelemetry->engine_load,
                    'intake_temp' => $latestTelemetry->intake_temp,
                    'voltage' => $latestTelemetry->voltage,
                    'boost' => $latestTelemetry->boost_pressure,
                    'rail' => $latestTelemetry->rail_pressure,
                    'egr' => $latestTelemetry->egr,
                    'dpf_in' => $latestTelemetry->dpf_temp_in,
                    'dpf_out' => $latestTelemetry->dpf_temp_out,
                    'soot' => $latestTelemetry->dpf_soot,
                ],
                'modem' => [
                    'signal' => $latestTelemetry->signal_strength,
                    'network' => $latestTelemetry->network_type,
                    'operator' => $latestTelemetry->operator,
                    'data_used' => $latestTelemetry->data_used,
                ],
                'ups' => [
                    'battery' => $latestTelemetry->battery_percent,
                    'charging' => $latestTelemetry->is_charging,
                ],
                'system' => [
                    'cpu_temp' => $latestTelemetry->cpu_temp,
                    'cpu' => $latestTelemetry->cpu_usage,
                    'memory' => $latestTelemetry->memory_usage,
                ],
            ] : null,
        ]);
    }

    /**
     * Actualizează intervalul de upload pentru device
     */
    public function updateInterval(Request $request, Device $device)
    {
        $request->validate([
            'interval' => 'required|integer|min:5|max:60'
        ]);

        $device->upload_interval = $request->interval;
        $device->save();

        return response()->json([
            'success' => true,
            'interval' => $device->upload_interval
        ]);
    }

    /**
     * Statistici pentru ultimele 24h
     */
    private function getDeviceStats(Device $device): array
    {
        $since = now()->subHours(24);

        $telemetry = DeviceTelemetry::where('device_id', $device->id)
            ->where('recorded_at', '>=', $since)
            ->get();

        return [
            'total_records' => $telemetry->count(),
            'max_speed' => $telemetry->max('speed'),
            'avg_speed' => round($telemetry->avg('speed') ?? 0, 1),
            'distance' => $this->calculateDistance($telemetry),
            'avg_fuel' => round($telemetry->avg('fuel_level') ?? 0, 1),
        ];
    }

    /**
     * Calculează distanța aproximativă din punctele GPS
     */
    private function calculateDistance($telemetry): float
    {
        $distance = 0;
        $prev = null;

        foreach ($telemetry as $t) {
            if (!$t->latitude || !$t->longitude) {
                continue;
            }
            if ($prev && $prev->latitude && $prev->longitude) {
                $distance += $this->haversine(
                    $prev->latitude, $prev->longitude,
                    $t->latitude, $t->longitude
                );
            }
            $prev = $t;
        }

        return round($distance, 2);
    }

    /**
     * Formula Haversine pentru distanța între 2 coordonate (km)
     */
    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $r * $c;
    }

    /**
     * Request DTC read from device
     */
    public function readDTC(Device $device)
    {
        $device->pending_command = 'read_dtc';
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Command queued. Codes will update on next device sync.',
            'codes' => $device->dtc_codes ?? [],
            'updated_at' => $device->dtc_updated_at?->diffForHumans(),
        ]);
    }

    /**
     * Request DTC clear from device
     */
    public function clearDTC(Device $device)
    {
        $device->pending_command = 'clear_dtc';
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Clear command queued. Will execute on next device sync.',
        ]);
    }

    /**
     * Update data retention period
     */
    public function updateRetention(Request $request, Device $device)
    {
        $validated = $request->validate([
            'retention_days' => 'required|integer|in:1,7,14,30,60,90'
        ]);

        $device->retention_days = $validated['retention_days'];
        $device->save();

        return response()->json([
            'success' => true,
            'retention_days' => $device->retention_days
        ]);
    }

    /**
     * Device settings page
     */
    public function settings(Device $device)
    {
        $settings = $device->settings ?? $this->getDefaultSettings();

        return view('ada-pi.device-settings', [
            'device' => $device,
            'settings' => $settings,
        ]);
    }

    /**
     * Save device settings
     */
    public function saveSettings(Request $request, Device $device)
    {
        $settings = [
            'cloud' => [
                'upload_url' => $request->input('cloud_upload_url'),
                'logs_url' => $request->input('cloud_logs_url'),
            ],
            'wifi' => [
                'enabled' => $request->has('wifi_enabled'),
                'ssid' => $request->input('wifi_ssid'),
                'password' => $request->input('wifi_password'),
                'dhcp' => $request->input('wifi_dhcp') == '1',
                'ip' => $request->input('wifi_ip'),
                'gateway' => $request->input('wifi_gateway'),
                'dns' => $request->input('wifi_dns'),
            ],
            'bluetooth' => [
                'enabled' => $request->has('bluetooth_enabled'),
                'discoverable' => $request->has('bluetooth_discoverable'),
                'name' => $request->input('bluetooth_name'),
            ],
            'modem' => [
                'apn' => $request->input('modem_apn'),
                'username' => $request->input('modem_username'),
                'password' => $request->input('modem_password'),
                'network_mode' => $request->input('modem_network_mode', 'auto'),
                'roaming' => $request->has('modem_roaming'),
            ],
            'gps' => [
                'enabled' => $request->has('gps_enabled'),
                'update_rate' => (int) $request->input('gps_update_rate', 1),
            ],
            'obd' => [
                'port' => $request->input('obd_port', 'auto'),
                'protocol' => $request->input('obd_protocol', 'auto'),
                'poll_interval' => (int) $request->input('obd_poll_interval', 2),
                'excluded_ports' => $request->input('obd_excluded_ports'),
            ],
            'ups' => [
                'low_threshold' => (int) $request->input('ups_low_threshold', 15),
                'auto_power_on' => $request->has('ups_auto_power_on'),
                'shutdown_delay' => (int) $request->input('ups_shutdown_delay', 30),
            ],
            'fan' => [
                'mode' => $request->input('fan_mode', 'auto'),
                'threshold' => (int) $request->input('fan_threshold', 50),
                'speed' => (int) $request->input('fan_speed', 100),
            ],
            'system' => [
                'timezone' => $request->input('system_timezone', 'UTC'),
                'hostname' => $request->input('system_hostname'),
                'auto_update' => $request->has('system_auto_update'),
                'reboot_schedule' => $request->input('system_reboot_schedule', 'disabled'),
            ],
        ];

        if ($request->filled('device_name')) {
            $device->device_name = $request->input('device_name');
        }

        $device->settings = $settings;
        $device->settings_updated_at = now();
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully. Will sync on next device upload.',
        ]);
    }

    /**
     * Send command to device
     */
    public function sendCommand(Request $request, Device $device)
    {
        $validated = $request->validate([
            'command' => 'required|string|in:reboot,factory_reset,update,restart_services'
        ]);

        $device->pending_command = $validated['command'];
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Command queued. Will execute on next device sync.',
            'command' => $validated['command'],
        ]);
    }

    /**
     * Get default settings structure
     */
    private function getDefaultSettings(): array
    {
        return [
            'cloud' => ['upload_url' => '', 'logs_url' => ''],
            'wifi' => ['enabled' => true, 'ssid' => '', 'password' => '', 'dhcp' => true, 'ip' => '', 'gateway' => '', 'dns' => ''],
            'bluetooth' => ['enabled' => true, 'discoverable' => false, 'name' => ''],
            'modem' => ['apn' => '', 'username' => '', 'password' => '', 'network_mode' => 'auto', 'roaming' => false],
            'gps' => ['enabled' => true, 'update_rate' => 1],
            'obd' => ['port' => 'auto', 'protocol' => 'auto', 'poll_interval' => 2, 'excluded_ports' => '/dev/ttyUSB2,/dev/ttyUSB3'],
            'ups' => ['low_threshold' => 15, 'auto_power_on' => true, 'shutdown_delay' => 30],
            'fan' => ['mode' => 'auto', 'threshold' => 50, 'speed' => 100],
            'system' => ['timezone' => 'UTC', 'hostname' => '', 'auto_update' => false, 'reboot_schedule' => 'disabled'],
        ];
    }

    /**
     * Acceptă un ADA-Pi
     */
    public function accept(Device $device): RedirectResponse
    {
        $device->status = 'active';
        $device->save();
        return back()->with('status', "Device {$device->device_name} approved.");
    }

    /**
     * Refuză un ADA-Pi
     */
    public function refuse(Device $device): RedirectResponse
    {
        $device->status = 'inactive';
        $device->save();
        return back()->with('status', "Device {$device->device_name} refused.");
    }
}