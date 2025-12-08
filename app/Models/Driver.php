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

    // =========================================================================
    // RELAȚII
    // =========================================================================

    /**
     * User account associated with this driver (if any)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Vehiculele asociate acestui șofer (many-to-many)
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicle')
            ->withPivot('assigned_from', 'assigned_to', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Employers (superuseri) care angajează acest șofer
     * many-to-many prin driver_superuser
     */
    public function employers()
    {
        return $this->belongsToMany(User::class, 'driver_superuser', 'driver_id', 'superuser_id')
            ->withPivot('hired_from', 'hired_to', 'is_active')
            ->withTimestamps();
    }

    /**
     * Employer activ (primul activ)
     */
    public function activeEmployers()
    {
        return $this->employers()->wherePivot('is_active', true);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Verifică dacă șoferul este angajat de un anumit superuser
     */
    public function isEmployedBy(User $superuser)
    {
        return $this->employers()
            ->where('superuser_id', $superuser->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Verifică dacă permisul este expirat
     */
    public function isLicenseExpired()
    {
        return $this->license_expiry_date && $this->license_expiry_date->isPast();
    }

    /**
     * Verifică dacă permisul expiră în curând (30 zile)
     */
    public function isLicenseExpiringSoon($days = 30)
    {
        if (!$this->license_expiry_date) {
            return false;
        }

        return $this->license_expiry_date->diffInDays(now()) <= $days 
            && !$this->license_expiry_date->isPast();
    }

    /**
     * Verifică dacă șoferul este activ
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Obține vehiculul primar (dacă există)
     */
    public function getPrimaryVehicle()
    {
        return $this->vehicles()->wherePivot('is_primary', true)->first();
    }

    /**
     * Obține device-urile prin vehiculele asociate
     */
    public function getDevices()
    {
        return $this->vehicles()
            ->whereNotNull('device_id')
            ->with('device')
            ->get()
            ->pluck('device')
            ->filter();
    }
}
