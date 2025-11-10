<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $pages = Page::published()->get();
        $locations = $this->getAvailableLocations();
        return view('admin.menus.create', compact('pages', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.url' => ['required', 'string', 'max:255'],
            'max_depth' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'location' => $request->location,
            'items' => $this->processMenuItems($request->items),
            'max_depth' => $request->max_depth,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log('created menu');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    public function show(Menu $menu)
    {
        return view('admin.menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $pages = Page::published()->get();
        $locations = $this->getAvailableLocations();
        return view('admin.menus.edit', compact('menu', 'pages', 'locations'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.url' => ['required', 'string', 'max:255'],
            'max_depth' => ['required', 'integer', 'min:1', 'max:5'],
            'is_active' => ['boolean'],
        ]);

        $menu->update([
            'name' => $request->name,
            'location' => $request->location,
            'items' => $this->processMenuItems($request->items),
            'max_depth' => $request->max_depth,
            'is_active' => $request->boolean('is_active', true),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log('updated menu');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log('deleted menu');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    public function reorder(Request $request, Menu $menu)
    {
        $request->validate([
            'items' => ['required', 'array'],
        ]);

        $menu->update([
            'items' => $this->processMenuItems($request->items),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log('reordered menu');

        return response()->json(['success' => true]);
    }

    private function processMenuItems($items)
    {
        return collect($items)->map(function ($item) {
            return [
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? '#',
                'target' => $item['target'] ?? '_self',
                'class' => $item['class'] ?? '',
                'icon' => $item['icon'] ?? '',
                'children' => isset($item['children']) ? $this->processMenuItems($item['children']) : [],
            ];
        })->toArray();
    }

    private function getAvailableLocations()
    {
        return [
            'header' => 'Header',
            'footer' => 'Footer',
            'sidebar' => 'Sidebar',
            'mobile' => 'Mobile',
        ];
    }
}