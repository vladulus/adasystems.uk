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
    ];

    /**
     * Shortcut: $vehicle->plate returns the registration number.
     */
    public function getPlateAttribute()
    {
        return $this->registration_number;
    }

    /**
     * Tracking device installed on this vehicle (if any).
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Platform user that owns/created this vehicle.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Drivers associated with this vehicle (many-to-many).
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicle')
            ->withTimestamps();
    }
}
