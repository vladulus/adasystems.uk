<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'license_number',
        'license_type',
        'license_issue_date',
        'license_expiry_date',
        'status',
        'user_id',
        'hire_date',
        'emergency_contact',
        'notes',
    ];

    protected $casts = [
        'date_of_birth'       => 'date',
        'license_issue_date'  => 'date',
        'license_expiry_date' => 'date',
        'hire_date'           => 'date',
    ];

    /**
     * Vehicles that this driver is assigned to (many-to-many).
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicle')
            ->withTimestamps();
    }
}
