<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceTelemetry;

class AdaPiDeviceStatusController extends Controller
{
    public function status(Request $request)
    {
        \Log::info('ADA-PI STATUS HIT', $request->all());

        $request->validate([
            'device_id' => 'required|string|max:50',
        ]);

        $device = Device::where('device_name', $request->device_id)->first();

        if (!$device) {
            // Prima dată - device nou, îl punem în pending
            $device = new Device();
            $device->device_name   = $request->device_id;
            $device->serial_number = null;
            $device->status        = 'pending';
            $device->owner_id      = null;
            $device->last_online   = now();
            $device->ip_address    = $request->ip();
            $device->save();
        } else {
            // Heartbeat - updatăm last_online + IP
            $device->last_online = now();
            $device->ip_address  = $request->ip();
            $device->save();

            // Salvăm telemetria doar dacă device-ul e activ
            if ($device->status === 'active') {
                $this->saveTelemetry($device, $request);
                
                // Sync config from device if settings are empty
                $this->syncConfigFromDevice($device, $request);
            }
        }

        $registered = in_array($device->status, ['active', 'inactive']);

        // Get pending command and clear it
        $pendingCommand = $device->pending_command;
        if ($pendingCommand) {
            $device->pending_command = null;
            $device->save();
        }

        // Build settings payload if settings exist and were updated
        $settingsPayload = null;
        if ($device->settings && $device->settings_updated_at) {
            $settingsPayload = [
                'version' => $device->settings_updated_at->timestamp,
                'data' => $device->settings,
            ];
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'registered' => $registered,
                'upload_interval' => $device->upload_interval ?? 15,
                'pending_command' => $pendingCommand,
                'settings' => $settingsPayload,
                'device' => [
                    'id'          => $device->id,
                    'device_name' => $device->device_name,
                    'status'      => $device->status,
                    'owner_id'    => $device->owner_id,
                ],
            ],
        ]);
    }

    /**
     * Salvează snapshot-ul de telemetrie în baza de date
     */
    private function saveTelemetry(Device $device, Request $request): void
    {
        $gps    = $request->input('gps', []);
        $obd    = $request->input('obd', []);
        $modem  = $request->input('modem', []);
        $ups    = $request->input('ups', []);
        $system = $request->input('system', []);

        // Save DTC codes if present
        if ($request->has('dtc')) {
            $device->dtc_codes = $request->input('dtc');
            $device->dtc_updated_at = now();
            $device->save();
        }

        // OBD values sunt nested sub 'values'
        $obdValues = $obd['values'] ?? $obd;

        // Modem signal e nested
        $modemSignal = $modem['signal'] ?? [];

        // Extrage timestamp-ul din request sau folosește now()
        $timestamp = $request->input('timestamp');
        $recordedAt = $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now();

        DeviceTelemetry::create([
            'device_id'   => $device->id,
            'recorded_at' => $recordedAt,

            // GPS
            'latitude'    => $this->nullIfZero($gps['latitude'] ?? $gps['lat'] ?? null),
            'longitude'   => $this->nullIfZero($gps['longitude'] ?? $gps['lon'] ?? $gps['lng'] ?? null),
            'altitude'    => $this->nullIfZero($gps['altitude'] ?? $gps['alt'] ?? null),
            'speed'       => $gps['speed'] ?? null,
            'heading'     => $gps['heading'] ?? $gps['course'] ?? null,
            'satellites'  => $gps['satellites'] ?? $gps['sats'] ?? null,

            // OBD Standard
            'rpm'           => $this->nullIfZero($obdValues['rpm'] ?? null),
            'vehicle_speed' => $this->nullIfZero($obdValues['speed'] ?? $obdValues['vehicle_speed'] ?? null),
            'coolant_temp'  => $this->nullIfZero($obdValues['coolant'] ?? $obdValues['coolant_temp'] ?? null),
            'fuel_level'    => $this->nullIfZero($obdValues['fuel_level'] ?? $obdValues['fuel'] ?? null),
            'throttle'      => $this->nullIfZero($obdValues['throttle'] ?? null),
            'engine_load'   => $this->nullIfZero($obdValues['load'] ?? $obdValues['engine_load'] ?? null),
            'intake_temp'   => $this->nullIfZero($obdValues['intake_temp'] ?? null),
            'voltage'       => $this->nullIfZero($obdValues['voltage'] ?? null),

            // OBD Diesel
            'boost_pressure' => $this->nullIfZero($obdValues['boost_pressure'] ?? $obdValues['boost'] ?? null),
            'rail_pressure'  => $this->nullIfZero($obdValues['rail_pressure'] ?? $obdValues['rail'] ?? null),
            'egr'            => $this->nullIfZero($obdValues['egr'] ?? null),
            'dpf_temp_in'    => $this->nullIfZero($obdValues['dpf_temp_in'] ?? null),
            'dpf_temp_out'   => $this->nullIfZero($obdValues['dpf_temp_out'] ?? null),
            'dpf_soot'       => $this->nullIfZero($obdValues['dpf_soot'] ?? null),

            // Modem
            'signal_strength' => $modemSignal['rssi'] ?? $modemSignal['rsrp'] ?? null,
            'network_type'    => $modem['network_mode'] ?? $modem['network'] ?? $modem['type'] ?? null,
            'operator'        => $modem['operator'] ?? null,
            'data_used'       => $modem['data_used'] ?? $modem['data'] ?? null,

            // UPS
            'battery_percent' => $ups['percent'] ?? $ups['battery'] ?? null,
            'battery_voltage' => $ups['voltage'] ?? null,
            'is_charging'     => $ups['charging'] ?? null,

            // System
            'cpu_temp'     => $system['cpu']['temp'] ?? $system['cpu_temp'] ?? $system['temp'] ?? null,
            'cpu_usage'    => $system['cpu']['usage'] ?? $system['cpu_usage'] ?? $system['cpu'] ?? null,
            'memory_usage' => $system['memory']['percent'] ?? $system['memory_usage'] ?? $system['memory'] ?? null,
            'disk_usage'   => $system['disk']['percent'] ?? $system['disk_usage'] ?? $system['disk'] ?? null,

            // Raw pentru backup
            'raw_data' => [
                'gps'       => $gps,
                'obd'       => $obd,
                'modem'     => $modem,
                'ups'       => $ups,
                'system'    => $system,
                'tacho'     => $request->input('tacho', []),
                'fan'       => $request->input('fan', []),
                'bluetooth' => $request->input('bluetooth', []),
                'network'   => $request->input('network', []),
                'config'    => $request->input('config', []),
            ],
        ]);

        \Log::debug("Telemetry saved for device {$device->device_name}");
    }

    /**
     * Sync config from device to settings for display
     * Only sync on first connect (when settings is null) - after that, cloud is master
     */
    private function syncConfigFromDevice(Device $device, Request $request): void
    {
        // Only sync if settings not yet initialized (first connect)
        if ($device->settings !== null) {
            return;
        }

        $config = $request->input('config');
        if (!$config) {
            return;
        }

        $settings = [];

        // WiFi settings
        if (isset($config['wifi'])) {
            $settings['wifi'] = [
                'enabled' => $config['wifi']['enabled'] ?? false,
                'ssid' => $config['wifi']['ssid'] ?? '',
                'password' => $config['wifi']['password'] ?? '',
                'dhcp' => $config['wifi']['dhcp'] ?? true,
                'ip' => $config['wifi']['ip'] ?? '',
                'gateway' => $config['wifi']['gateway'] ?? '',
                'dns' => $config['wifi']['dns'] ?? '',
            ];
        }

        // Bluetooth settings
        if (isset($config['bluetooth'])) {
            $settings['bluetooth'] = [
                'enabled' => $config['bluetooth']['enabled'] ?? true,
                'discoverable' => $config['bluetooth']['discoverable'] ?? false,
                'name' => $config['bluetooth']['name'] ?? '',
            ];
        }

        // Modem settings
        if (isset($config['modem'])) {
            $settings['modem'] = [
                'apn' => $config['modem']['apn'] ?? '',
                'apn_username' => $config['modem']['apn_username'] ?? '',
                'apn_password' => $config['modem']['apn_password'] ?? '',
                'network_mode' => $config['modem']['network_mode'] ?? 'auto',
                'roaming' => $config['modem']['roaming'] ?? false,
                'failover_enabled' => $config['modem']['failover_enabled'] ?? true,
            ];
        }

        // GPS settings
        if (isset($config['gps'])) {
            $settings['gps'] = [
                'enabled' => $config['gps']['enabled'] ?? true,
                'update_rate' => $config['gps']['update_rate'] ?? 1,
            ];
        }

        // OBD settings
        if (isset($config['obd'])) {
            $settings['obd'] = [
                'enabled' => $config['obd']['enabled'] ?? false,
                'connection' => $config['obd']['connection'] ?? 'none',
                'bluetooth_mac' => $config['obd']['bluetooth_mac'] ?? '',
                'usb_port' => $config['obd']['usb_port'] ?? '',
                'protocol' => $config['obd']['protocol'] ?? 'auto',
                'poll_interval' => $config['obd']['poll_interval'] ?? 2,
            ];
        }

        // UPS settings
        if (isset($config['ups'])) {
            $settings['ups'] = [
                'type' => $config['ups']['type'] ?? 'none',
                'shutdown_pct' => $config['ups']['shutdown_pct'] ?? 15,
                'auto_power_on' => $config['ups']['auto_power_on'] ?? true,
                'shutdown_delay' => $config['ups']['shutdown_delay'] ?? 30,
            ];
        }

        // Fan settings
        if (isset($config['fan'])) {
            $settings['fan'] = [
                'mode' => $config['fan']['mode'] ?? 'auto',
                'threshold' => $config['fan']['threshold'] ?? 50,
                'speed' => $config['fan']['speed'] ?? 100,
            ];
        }

        // System settings
        if (isset($config['system'])) {
            $settings['system'] = [
                'timezone' => $config['system']['timezone'] ?? 'UTC',
                'hostname' => $config['system']['hostname'] ?? '',
                'auto_update' => $config['system']['auto_update'] ?? false,
                'reboot_schedule' => $config['system']['reboot_schedule'] ?? 'disabled',
            ];
        }

        // Save settings for display (but NOT settings_updated_at - that's only set from UI)
        $device->settings = $settings;
        $device->save();
    }

    /**
     * Returnează null dacă valoarea e 0 (pentru GPS fără fix)
     */
    private function nullIfZero($value)
    {
        if ($value === 0 || $value === 0.0 || $value === '0') {
            return null;
        }
        return $value;
    }
}