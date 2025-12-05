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
        // dacă în viitor vrei să salvezi created_by / updated_by prin fill():
        // 'created_by',
        // 'updated_by',
    ];

    protected $casts = [
        'last_online' => 'datetime',
    ];

    /**
     * Alias simplu: $device->name => $device->device_name
     * (ca să nu crape UI-ul dacă folosește "name")
     */
    public function getNameAttribute()
    {
        return $this->device_name;
    }

    /**
     * Get the owner (admin/superuser) of this device
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the vehicle where this device is installed
     */
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'device_id');
    }

    /**
     * User-ul care a creat device-ul
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User-ul care a modificat ultima dată device-ul
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if device is currently assigned to a vehicle
     */
    public function isAssigned()
    {
        return !is_null($this->vehicle);
    }

    /**
     * Check if device is online (last_online within 5 minutes)
     */
    public function isOnline()
    {
        if (is_null($this->last_online)) {
            return false;
        }

        return $this->last_online->diffInMinutes(now()) <= 5;
    }
}
