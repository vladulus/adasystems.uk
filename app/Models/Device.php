<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'serial_number',
        'owner_id',
        'status',
        'last_online',
        'ip_address',
        'upload_interval',
        'pending_command',
        'dtc_codes',
        'dtc_updated_at',
        'retention_days',
        'settings',
        'settings_updated_at',
    ];

    protected $casts = [
        'last_online' => 'datetime',
        'dtc_codes' => 'array',
        'dtc_updated_at' => 'datetime',
        'settings' => 'array',
        'settings_updated_at' => 'datetime',
    ];

    /**
     * Alias simplu: $device->name => $device->device_name
     */
    public function getNameAttribute()
    {
        return $this->device_name;
    }

    // =========================================================================
    // RELAȚII
    // =========================================================================

    /**
     * Owner (superuser) - un device are un singur proprietar
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Adminii care administrează acest device
     * many-to-many prin admin_device
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'admin_device', 'device_id', 'admin_id')
            ->withTimestamps();
    }

    /**
     * Vehiculul unde este instalat device-ul (1:1)
     */
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'device_id');
    }

    /**
     * Telemetrie - toate înregistrările
     */
    public function telemetry()
    {
        return $this->hasMany(DeviceTelemetry::class);
    }

    /**
     * Ultima înregistrare telemetrie
     */
    public function latestTelemetry()
    {
        return $this->hasOne(DeviceTelemetry::class)->latestOfMany('recorded_at');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Verifică dacă device-ul este alocat unei mașini
     */
    public function isAssigned()
    {
        return !is_null($this->vehicle);
    }

    /**
     * Verifică dacă device-ul este online (ultimele 5 minute)
     */
    public function isOnline()
    {
        if (is_null($this->last_online)) {
            return false;
        }

        return $this->last_online->diffInMinutes(now()) <= 5;
    }

    /**
     * Verifică dacă device-ul are owner
     */
    public function hasOwner()
    {
        return !is_null($this->owner_id);
    }

    /**
     * Verifică dacă device-ul este administrat de un anumit admin
     */
    public function isManagedBy(User $admin)
    {
        return $this->admins()->where('admin_id', $admin->id)->exists();
    }

    /**
     * Alocă device-ul la un vehicul și moștenește owner/admin
     */
    public function assignToVehicle(Vehicle $vehicle, ?User $assignedBy = null)
    {
        // Setează vehiculul
        $vehicle->device_id = $this->id;
        $vehicle->save();

        // Moștenește owner de la vehicul
        if ($vehicle->owner_id) {
            $this->owner_id = $vehicle->owner_id;
            $this->save();
        }

        // Dacă a fost alocat de un admin, adaugă-l în lista de admini
        if ($assignedBy && $assignedBy->hasRole('admin')) {
            $this->admins()->syncWithoutDetaching([$assignedBy->id]);
        }

        return $this;
    }

    /**
     * Detașează device-ul de la vehicul
     */
    public function detachFromVehicle()
    {
        if ($this->vehicle) {
            $vehicle = $this->vehicle;
            $vehicle->device_id = null;
            $vehicle->save();
        }

        return $this;
    }
}