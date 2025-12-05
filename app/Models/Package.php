<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'monthly_price',
        'features',
        'max_drivers',
        'is_active',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'features' => 'array',
        'max_drivers' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    
    // Subscriptions using this package
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Helper methods
     */
    
    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return '£' . number_format($this->monthly_price, 2);
    }

    // Check if package includes a specific feature
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    // Get list of features as string
    public function getFeaturesListAttribute()
    {
        return implode(', ', $this->features ?? []);
    }
}
