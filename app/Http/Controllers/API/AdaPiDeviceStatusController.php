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
     * Sync config from device to settings if settings are empty
     */
    private function syncConfigFromDevice(Device $device, Request $request): void
    {
        $config = $request->input('config');
        if (!$config) {
            return;
        }

        $settings = $device->settings ?? [];
        $updated = false;

        // Modem settings - sync if cloud is empty
        if (isset($config['modem'])) {
            if (!isset($settings['modem'])) {
                $settings['modem'] = [];
            }
            
            if (empty($settings['modem']['apn']) && !empty($config['modem']['apn'])) {
                $settings['modem']['apn'] = $config['modem']['apn'];
                $settings['modem']['apn_username'] = $config['modem']['apn_username'] ?? '';
                $settings['modem']['apn_password'] = $config['modem']['apn_password'] ?? '';
                $settings['modem']['failover_enabled'] = $config['modem']['failover_enabled'] ?? true;
                $updated = true;
                \Log::info("Synced modem config from device {$device->device_name}");
            }
        }

        // OBD settings - sync if cloud is empty
        if (isset($config['obd'])) {
            if (!isset($settings['obd'])) {
                $settings['obd'] = [];
            }
            
            if (!isset($settings['obd']['enabled']) && isset($config['obd']['enabled'])) {
                $settings['obd']['enabled'] = $config['obd']['enabled'];
                $settings['obd']['connection'] = $config['obd']['connection'] ?? 'none';
                $settings['obd']['bluetooth_mac'] = $config['obd']['bluetooth_mac'] ?? '';
                $settings['obd']['usb_port'] = $config['obd']['usb_port'] ?? '';
                $updated = true;
                \Log::info("Synced OBD config from device {$device->device_name}");
            }
        }

        // UPS settings - sync if cloud is empty
        if (isset($config['ups'])) {
            if (!isset($settings['ups'])) {
                $settings['ups'] = [];
            }
            
            if (empty($settings['ups']['type']) && !empty($config['ups']['type'])) {
                $settings['ups']['type'] = $config['ups']['type'];
                $settings['ups']['shutdown_pct'] = $config['ups']['shutdown_pct'] ?? 10;
                $updated = true;
                \Log::info("Synced UPS config from device {$device->device_name}");
            }
        }

        if ($updated) {
            $device->settings = $settings;
            $device->settings_updated_at = now();
            $device->save();
        }
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