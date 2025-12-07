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
            }
        }

        $registered = in_array($device->status, ['active', 'inactive']);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'registered' => $registered,
                'upload_interval' => $device->upload_interval ?? 15,
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

            // OBD (nested under 'values')
            'rpm'           => $this->nullIfZero($obdValues['rpm'] ?? null),
            'vehicle_speed' => $this->nullIfZero($obdValues['speed'] ?? $obdValues['vehicle_speed'] ?? null),
            'coolant_temp'  => $this->nullIfZero($obdValues['coolant'] ?? $obdValues['coolant_temp'] ?? null),
            'fuel_level'    => $this->nullIfZero($obdValues['fuel_level'] ?? $obdValues['fuel'] ?? null),
            'throttle'      => $this->nullIfZero($obdValues['throttle'] ?? null),
            'engine_load'   => $this->nullIfZero($obdValues['load'] ?? $obdValues['engine_load'] ?? null),

            // Modem (signal nested)
            'signal_strength' => $modemSignal['rssi'] ?? $modemSignal['rsrp'] ?? null,
            'network_type'    => $modem['network_mode'] ?? $modem['network'] ?? $modem['type'] ?? null,
            'operator'        => $modem['operator'] ?? null,

            // UPS
            'battery_percent' => $ups['percent'] ?? $ups['battery'] ?? null,
            'battery_voltage' => $ups['voltage'] ?? null,
            'is_charging'     => $ups['charging'] ?? null,

            // System (nested structure: system.cpu.temp, system.memory.percent, etc.)
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
            ],
        ]);

        \Log::debug("Telemetry saved for device {$device->device_name}");
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