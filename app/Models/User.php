<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',              // NOU
        'department',         // NOU
        'parent_id',
        'is_active',
        'status',             // NOU
        'vehicle_id',
        'last_login',
        'last_login_at',      // NOU
        'email_verified_at',
        'created_by',         // NOU
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'status' => 'string',           // NOU
        'last_login' => 'datetime',
        'last_login_at' => 'datetime',  // NOU
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->roles->first()?->name ?? 'user',
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'parent_id' => $this->parent_id,
            'vehicle_id' => $this->vehicle_id,
            'device_id' => $this->vehicle?->device?->device_name ?? null, // For backward compatibility with Pi
        ];
    }

    /**
     * Relationships
     */
    
    // Parent user (for drivers belonging to a superuser)
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // Child users (drivers that belong to this superuser)
    public function drivers()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Vehicle assigned to this user (driver)
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    // Subscription (for superusers only)
    public function subscription()
    {
        return $this->hasOne(UserSubscription::class, 'superuser_id');
    }

    /**
     * Helper methods
     */
	public function isRoot(): bool
    {
        return $this->email === config('ada.root_email');
    }

    /**
     * Superadmin „efectiv”: root sau user cu rolul super-admin.
     */
    public function isEffectiveSuperAdmin(): bool
    {
        return $this->isRoot() || $this->hasRole('super-admin');
    }
    
    // Check if user is a super-admin or admin
    public function isAdmin()
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    // Check if user is a super-admin
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    // Check if user is a client (superuser)
    public function isSuperuser()
    {
        return $this->hasRole('superuser');
    }

    // Check if user is a driver
    public function isDriver()
    {
        return $this->hasRole('user') && !is_null($this->parent_id);
    }

    // Get all drivers for this superuser
    public function getDrivers()
    {
        if ($this->isSuperuser()) {
            return $this->drivers;
        }
        return collect([]);
    }

    // Get parent superuser (for drivers)
    public function getSuperuser()
    {
        if ($this->isDriver()) {
            return $this->parent;
        }
        return null;
    }

    // Get the device (Pi) for this user
    public function getDevice()
    {
        return $this->vehicle?->device;
    }

    /**
     * Get all devices visible to this user based on role
     */
    public function getVisibleDevices()
    {
        if ($this->isSuperAdmin()) {
            // Super-admin sees ALL devices
            return Device::all();
        }

        if ($this->hasRole('admin')) {
            // Admin sees devices assigned to them
            return Device::where('owner_id', $this->id)->get();
        }

        if ($this->isSuperuser()) {
            // Superuser sees devices in their vehicles
            return Device::whereHas('vehicle', function($query) {
                $query->where('owner_id', $this->id);
            })->get();
        }

        // Driver sees only their own device
        if ($this->vehicle && $this->vehicle->device) {
            return collect([$this->vehicle->device]);
        }

        return collect([]);
    }
	/**
     * Management system relationships
     */
    
    // Creator of this user
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Driver profile (if this user is linked to a driver)
    public function driver()
    {
        return $this->hasOne(Driver::class, 'user_id');
    }

    // Devices created by this user
    public function devices()
    {
        return $this->hasMany(Device::class, 'created_by');
    }

    // Vehicles created by this user
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'created_by');
    }
}
