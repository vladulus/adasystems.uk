<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'superuser_id',
        'package_id',
        'drivers_allocated',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'drivers_allocated' => 'integer',
    ];

    /**
     * Relationships
     */
    
    // The superuser (client) who owns this subscription
    public function superuser()
    {
        return $this->belongsTo(User::class, 'superuser_id');
    }

    // The package this subscription is for
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Helper methods
     */
    
    // Check if subscription is currently active and not expired
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    // Get remaining driver slots
    public function getRemainingDriverSlots()
    {
        $allocated = $this->drivers_allocated ?? 0;
        $max = $this->package->max_drivers ?? 0;
        
        return max(0, $max - $allocated);
    }

    // Check if can allocate more drivers
    public function canAllocateDriver()
    {
        return $this->getRemainingDriverSlots() > 0;
    }
}
