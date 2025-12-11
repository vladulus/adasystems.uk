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
        'phone',
        'department',
        'parent_id',
        'is_active',
        'status',
        'vehicle_id',
        'device_id',
        'last_login',
        'last_login_at',
        'email_verified_at',
        'created_by',
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
        'status' => 'string',
        'last_login' => 'datetime',
        'last_login_at' => 'datetime',
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
            'device_id' => $this->vehicle?->device?->device_name ?? null,
        ];
    }

    // =========================================================================
    // RELAȚII EXISTENTE
    // =========================================================================

    /**
     * Parent user (for drivers belonging to a superuser)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Child users (drivers that belong to this superuser) - LEGACY
     */
    public function childUsers()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Vehicle assigned to this user (driver)
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Subscription (for superusers only)
     */
    public function subscription()
    {
        return $this->hasOne(UserSubscription::class, 'superuser_id');
    }

    /**
     * Creator of this user
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Driver profile (if this user is linked to a driver)
     */
    public function driverProfile()
    {
        return $this->hasOne(Driver::class, 'user_id');
    }

    // =========================================================================
    // RELAȚII NOI - PIVOT TABLES
    // =========================================================================

    /**
     * Pentru ADMIN: Superuserii (clienții) pe care îi administrează
     * many-to-many prin admin_superuser
     */
    public function managedSuperusers()
    {
        return $this->belongsToMany(User::class, 'admin_superuser', 'admin_id', 'superuser_id')
            ->withTimestamps();
    }

    /**
     * Pentru SUPERUSER: Adminii care îl administrează
     * many-to-many prin admin_superuser
     */
    public function managingAdmins()
    {
        return $this->belongsToMany(User::class, 'admin_superuser', 'superuser_id', 'admin_id')
            ->withTimestamps();
    }

    /**
     * Pentru ADMIN: Device-urile pe care le administrează
     * many-to-many prin admin_device
     */
    public function managedDevices()
    {
        return $this->belongsToMany(Device::class, 'admin_device', 'admin_id', 'device_id')
            ->withTimestamps();
    }

    /**
     * Pentru SUPERUSER: Device-urile pe care le deține (owner)
     * one-to-many (un device are un singur owner)
     */
    public function ownedDevices()
    {
        return $this->hasMany(Device::class, 'owner_id');
    }

    /**
     * Pentru SUPERUSER: Vehiculele pe care le deține
     * one-to-many
     */
    public function ownedVehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    /**
     * Pentru SUPERUSER: Driverii pe care îi angajează
     * many-to-many prin driver_superuser
     */
    public function employedDrivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_superuser', 'superuser_id', 'driver_id')
            ->withPivot('hired_from', 'hired_to', 'is_active')
            ->withTimestamps();
    }

    // =========================================================================
    // HELPER METHODS - ROLE CHECKS
    // =========================================================================

    public function isRoot(): bool
    {
        return $this->email === config('ada.root_email');
    }

    /**
     * Superadmin „efectiv": root sau user cu rolul super-admin.
     */
    public function isEffectiveSuperAdmin(): bool
    {
        return $this->isRoot() || $this->hasRole('super-admin');
    }

    public function isAdmin()
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function isSuperuser()
    {
        return $this->hasRole('superuser');
    }

    public function isDriver()
    {
        return $this->hasRole('user');
    }

    // =========================================================================
    // HELPER METHODS - VISIBILITY (based on PERMISSIONS + SCOPE)
    // =========================================================================

    /**
     * Get all devices visible to this user based on permissions and scope
     */
    public function getVisibleDevices()
    {
        // Root bypasses everything
        if ($this->isRoot()) {
            return Device::all();
        }

        // Check scope permission (kill switch)
        $hasScopeAll = $this->can('devices.scope.all');
        $hasScopeOwn = $this->can('devices.scope.own');

        // No scope = no access (kill switch active)
        if (!$hasScopeAll && !$hasScopeOwn) {
            return collect([]);
        }

        // scope.all = can see everything based on role hierarchy
        if ($hasScopeAll) {
            if ($this->isEffectiveSuperAdmin()) {
                return Device::all();
            }
            if ($this->hasRole('admin')) {
                return $this->managedDevices;
            }
            if ($this->isSuperuser()) {
                return $this->ownedDevices;
            }
            if ($this->vehicle && $this->vehicle->device) {
                return collect([$this->vehicle->device]);
            }
            return collect([]);
        }

        // scope.own = can see only own devices
        if ($hasScopeOwn) {
            if ($this->hasRole('admin')) {
                return $this->managedDevices;
            }
            if ($this->isSuperuser()) {
                return $this->ownedDevices;
            }
            if ($this->vehicle && $this->vehicle->device) {
                return collect([$this->vehicle->device]);
            }
            return collect([]);
        }

        return collect([]);
    }

    /**
     * Get all vehicles visible to this user based on permissions and scope
     */
    public function getVisibleVehicles()
    {
        // Root bypasses everything
        if ($this->isRoot()) {
            return Vehicle::all();
        }

        // Check scope permission (kill switch)
        $hasScopeAll = $this->can('vehicles.scope.all');
        $hasScopeOwn = $this->can('vehicles.scope.own');

        // No scope = no access (kill switch active)
        if (!$hasScopeAll && !$hasScopeOwn) {
            return collect([]);
        }

        // scope.all = can see everything based on role hierarchy
        if ($hasScopeAll) {
            if ($this->isEffectiveSuperAdmin()) {
                return Vehicle::all();
            }
            if ($this->hasRole('admin')) {
                $superuserIds = $this->managedSuperusers->pluck('id');
                return Vehicle::whereIn('owner_id', $superuserIds)->get();
            }
            if ($this->isSuperuser()) {
                return $this->ownedVehicles;
            }
            if ($this->vehicle) {
                return collect([$this->vehicle]);
            }
            return collect([]);
        }

        // scope.own = can see only own vehicles
        if ($hasScopeOwn) {
            if ($this->hasRole('admin')) {
                $superuserIds = $this->managedSuperusers->pluck('id');
                return Vehicle::whereIn('owner_id', $superuserIds)->get();
            }
            if ($this->isSuperuser()) {
                return $this->ownedVehicles;
            }
            if ($this->vehicle) {
                return collect([$this->vehicle]);
            }
            return collect([]);
        }

        return collect([]);
    }

    /**
     * Get all drivers visible to this user based on permissions and scope
     */
    public function getVisibleDrivers()
    {
        // Root bypasses everything
        if ($this->isRoot()) {
            return Driver::all();
        }

        // Check scope permission (kill switch)
        $hasScopeAll = $this->can('drivers.scope.all');
        $hasScopeOwn = $this->can('drivers.scope.own');

        // No scope = no access (kill switch active)
        if (!$hasScopeAll && !$hasScopeOwn) {
            return collect([]);
        }

        // scope.all = can see everything based on role hierarchy
        if ($hasScopeAll) {
            if ($this->isEffectiveSuperAdmin()) {
                return Driver::all();
            }
            if ($this->hasRole('admin')) {
                $superuserIds = $this->managedSuperusers->pluck('id');
                return Driver::whereHas('employers', function($q) use ($superuserIds) {
                    $q->whereIn('superuser_id', $superuserIds);
                })->get();
            }
            if ($this->isSuperuser()) {
                return $this->employedDrivers;
            }
            return collect([]);
        }

        // scope.own = can see only own drivers
        if ($hasScopeOwn) {
            if ($this->hasRole('admin')) {
                $superuserIds = $this->managedSuperusers->pluck('id');
                return Driver::whereHas('employers', function($q) use ($superuserIds) {
                    $q->whereIn('superuser_id', $superuserIds);
                })->get();
            }
            if ($this->isSuperuser()) {
                return $this->employedDrivers;
            }
            return collect([]);
        }

        return collect([]);
    }

    /**
     * Get all superusers visible to this user based on permissions and scope
     */
    public function getVisibleSuperusers()
    {
        // Root bypasses everything
        if ($this->isRoot()) {
            return User::role('superuser')->get();
        }

        // Check scope permission (kill switch)
        $hasScopeAll = $this->can('users.scope.all');
        $hasScopeOwn = $this->can('users.scope.own');

        // No scope = no access (kill switch active)
        if (!$hasScopeAll && !$hasScopeOwn) {
            return collect([]);
        }

        if ($hasScopeAll) {
            if ($this->isEffectiveSuperAdmin()) {
                return User::role('superuser')->get();
            }
            if ($this->hasRole('admin')) {
                return $this->managedSuperusers;
            }
            return collect([]);
        }

        if ($hasScopeOwn) {
            if ($this->hasRole('admin')) {
                return $this->managedSuperusers;
            }
            return collect([]);
        }

        return collect([]);
    }

    /**
     * Get the device (Pi) for this user - for drivers
     */
    public function getDevice()
    {
        return $this->vehicle?->device;
    }
}
