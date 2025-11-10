<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $groups = Setting::select('group')->distinct()->pluck('group');
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        
        return view('admin.settings.index', compact('groups', 'settings'));
    }

    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'value' => $this->getValidationRule($setting),
        ]);

        $setting->update(['value' => $request->value]);

        // Clear settings cache
        Cache::forget('app_settings');

        activity()
            ->causedBy(auth()->user())
            ->performedOn($setting)
            ->log('updated setting');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        // Clear settings cache
        Cache::forget('app_settings');

        activity()
            ->causedBy(auth()->user())
            ->log('updated multiple settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function create()
    {
        $types = ['string', 'text', 'boolean', 'integer', 'json'];
        $groups = Setting::select('group')->distinct()->pluck('group');
        
        return view('admin.settings.create', compact('types', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value' => ['required'],
            'type' => ['required', 'in:string,text,boolean,integer,json'],
            'group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::create($request->all());

        // Clear settings cache
        Cache::forget('app_settings');

        activity()
            ->causedBy(auth()->user())
            ->log('created new setting');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        // Clear settings cache
        Cache::forget('app_settings');

        activity()
            ->causedBy(auth()->user())
            ->performedOn($setting)
            ->log('deleted setting');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    private function getValidationRule(Setting $setting)
    {
        return match($setting->type) {
            'boolean' => ['required', 'boolean'],
            'integer' => ['required', 'integer'],
            'json' => ['required', 'json'],
            'text' => ['required', 'string', 'max:65535'],
            default => ['required', 'string', 'max:255'],
        };
    }
}