<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'group'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Setting {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function getValueAttribute($value)
    {
        return match($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match($this->type) {
            'boolean' => (bool) $value ? '1' : '0',
            'integer' => (int) $value,
            'json' => is_array($value) ? json_encode($value) : $value,
            default => $value,
        };
    }

    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue($key, $value)
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $setting->save();
        return $setting;
    }
}