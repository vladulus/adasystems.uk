<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Device;
use App\Models\User;
use App\Models\Driver;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'device_id',
        'owner_id',
        'make',
        'model',
        'year',
        'vin',
        'status',
        // DVLA VES fields
        'colour',
        'fuel_type',
        'engine_capacity',
        'co2_emissions',
        'tax_status',
        'tax_due_date',
        'mot_status',
        'mot_expiry_date',
        'euro_status',
        'wheelplan',
        'revenue_weight',
        'first_registered_at',
    ];

    protected $casts = [
        'tax_due_date' => 'date',
        'mot_expiry_date' => 'date',
        'first_registered_at' => 'date',
        'engine_capacity' => 'integer',
        'co2_emissions' => 'integer',
        'revenue_weight' => 'integer',
    ];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Shortcut: $vehicle->plate returns the registration number.
     */
    public function getPlateAttribute()
    {
        return $this->registration_number;
    }

    /**
     * Full name: marca + model + an
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->make, $this->model, $this->year]);
        return implode(' ', $parts) ?: $this->registration_number;
    }

    // =========================================================================
    // RELAȚII
    // =========================================================================

    /**
     * Device-ul instalat pe această mașină (1:1)
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Owner (superuser) - proprietarul mașinii
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Șoferii asociați acestui vehicul (many-to-many)
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicle')
            ->withPivot('assigned_from', 'assigned_to', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Șoferul primar al vehiculului
     */
    public function primaryDriver()
    {
        return $this->drivers()->wherePivot('is_primary', true)->first();
    }

    /**
     * Șoferii activi (fără assigned_to sau assigned_to în viitor)
     */
    public function activeDrivers()
    {
        return $this->drivers()
            ->where(function($q) {
                $q->whereNull('driver_vehicle.assigned_to')
                  ->orWhere('driver_vehicle.assigned_to', '>=', now());
            });
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Verifică dacă vehiculul are device instalat
     */
    public function hasDevice()
    {
        return !is_null($this->device_id);
    }

    /**
     * Verifică dacă vehiculul este activ
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Verifică dacă vehiculul aparține unui anumit superuser
     */
    public function isOwnedBy(User $superuser)
    {
        return $this->owner_id === $superuser->id;
    }

    /**
     * Verifică dacă un anumit șofer e asociat acestui vehicul
     */
    public function hasDriver(Driver $driver)
    {
        return $this->drivers()->where('driver_id', $driver->id)->exists();
    }

    /**
     * Obține ultimele date telemetrie de la device
     */
    public function getLatestTelemetry()
    {
        return $this->device?->latestTelemetry;
    }

    /**
     * Verifică dacă vehiculul este online (device online)
     */
    public function isOnline()
    {
        return $this->device?->isOnline() ?? false;
    }

    /**
     * Obține adminii care administrează acest vehicul (prin owner)
     */
    public function getAdmins()
    {
        if (!$this->owner) {
            return collect([]);
        }

        return $this->owner->managingAdmins;
    }
}
