<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeviceTelemetry extends Model
{
    protected $table = 'device_telemetry';
    protected $fillable = [
        'device_id',
        'recorded_at',
        // GPS
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'heading',
        'satellites',
        // OBD Standard
        'rpm',
        'vehicle_speed',
        'coolant_temp',
        'fuel_level',
        'throttle',
        'engine_load',
        'intake_temp',
        'voltage',
        // OBD Diesel
        'boost_pressure',
        'rail_pressure',
        'egr',
        'dpf_temp_in',
        'dpf_temp_out',
        'dpf_soot',
        // Modem
        'signal_strength',
        'network_type',
        'operator',
        'data_used',
        // UPS
        'battery_percent',
        'battery_voltage',
        'is_charging',
        // System
        'cpu_temp',
        'cpu_usage',
        'memory_usage',
        'disk_usage',
        // Raw
        'raw_data',
    ];
    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_charging' => 'boolean',
        'raw_data' => 'array',
    ];
    /**
     * Device-ul căruia îi aparține telemetria
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
    /**
     * Verifică dacă avem coordonate GPS valide
     */
    public function hasGps(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
    /**
     * Verifică dacă avem date OBD
     */
    public function hasObd(): bool
    {
        return !is_null($this->rpm) || !is_null($this->vehicle_speed);
    }
}