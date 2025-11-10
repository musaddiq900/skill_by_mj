<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Menu extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'location',
        'items',
        'is_active',
        'max_depth',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'location', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Menu {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLocation($query, $location)
    {
        return $query->where('location', $location)->active();
    }

    public function getMenuItemsAttribute()
    {
        return collect($this->items)->map(function ($item) {
            return [
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? '#',
                'target' => $item['target'] ?? '_self',
                'children' => $item['children'] ?? [],
                'class' => $item['class'] ?? '',
                'icon' => $item['icon'] ?? '',
            ];
        });
    }

    public static function getByLocation($location)
    {
        return static::location($location)->first();
    }
}