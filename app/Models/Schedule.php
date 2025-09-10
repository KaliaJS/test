<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory,
        HasUuids;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scheduleItems()
    {
        return $this->hasMany(ScheduleItem::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * Scopes
     */

    public function scopeWithRelations($query)
    {
        return $query->with([
            'truck',
            'scheduleItems.schedulePlace',
            'scheduleItems.hours'
        ]);
    }
}
