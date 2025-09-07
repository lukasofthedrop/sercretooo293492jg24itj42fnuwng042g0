<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateSettings extends Model
{
    protected $fillable = [
        'user_id',
        'revshare_percentage',
        'revshare_display',
        'cpa_value',
        'ngr_minimum',
        'tier',
        'visible_metrics',
        'can_see_ngr',
        'can_see_deposits',
        'can_see_losses',
        'can_see_reports',
        'calculation_period',
        'is_active'
    ];

    protected $casts = [
        'visible_metrics' => 'array',
        'can_see_ngr' => 'boolean',
        'can_see_deposits' => 'boolean',
        'can_see_losses' => 'boolean',
        'can_see_reports' => 'boolean',
        'is_active' => 'boolean',
        'revshare_percentage' => 'decimal:2',
        'revshare_display' => 'decimal:2',
        'cpa_value' => 'decimal:2',
        'ngr_minimum' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'revshare_percentage' => 20.00,
                'revshare_display' => 20.00,
                'cpa_value' => 50.00,
                'ngr_minimum' => 100.00,
                'tier' => 'bronze',
                'visible_metrics' => [],
                'can_see_ngr' => false,
                'can_see_deposits' => true,
                'can_see_losses' => true,
                'can_see_reports' => false,
                'calculation_period' => 'monthly',
                'is_active' => true
            ]
        );
    }

    public function getTierBadgeColorAttribute()
    {
        return match($this->tier) {
            'bronze' => '#CD7F32',
            'silver' => '#C0C0C0',
            'gold' => '#FFD700',
            'custom' => '#8B008B',
            default => '#808080'
        };
    }

    public function canViewMetric($metric)
    {
        $metricPermissions = [
            'ngr' => $this->can_see_ngr,
            'deposits' => $this->can_see_deposits,
            'losses' => $this->can_see_losses,
            'reports' => $this->can_see_reports
        ];

        return $metricPermissions[$metric] ?? false;
    }
}