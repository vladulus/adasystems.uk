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
                ],
                'modem' => [
                    'signal' => $latestTelemetry->signal_strength,
                    'network' => $latestTelemetry->network_type,
                    'operator' => $latestTelemetry->operator,
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
            // Skip if current has no valid coords
            if (!$t->latitude || !$t->longitude) {
                continue;
            }
        
            // Calculate distance only if prev also has valid coords
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
        $r = 6371; // km
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

        // Return current codes if available
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
     * Acceptă un ADA-Pi:
     * - schimbă status în "active"
     */
    public function accept(Device $device): RedirectResponse
    {
        $device->status = 'active';
        $device->save();

        return back()->with('status', "Device {$device->device_name} approved.");
    }

    /**
     * Refuză un ADA-Pi:
     * - marcăm status "inactive"
     */
    public function refuse(Device $device): RedirectResponse
    {
        $device->status = 'inactive';
        $device->save();

        return back()->with('status', "Device {$device->device_name} refused.");
    }
}