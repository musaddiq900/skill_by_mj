<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CustomRoute extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'path',
        'controller',
        'method',
        'middleware',
        'active',
        'order',
    ];

    protected $casts = [
        'middleware' => 'array',
        'active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'path', 'active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Custom Route {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getMiddlewareArrayAttribute()
    {
        return $this->middleware ?? [];
    }

    public function isValidController()
    {
        return class_exists($this->controller) && method_exists($this->controller, $this->method);
    }
}