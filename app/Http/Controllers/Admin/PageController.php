<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('creator', 'updater')->latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $layouts = $this->getAvailableLayouts();
        return view('admin.pages.create', compact('layouts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'layout' => ['required', 'string', 'in:default,full-width,sidebar-left,sidebar-right'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft,archived'],
            'published_at' => ['nullable', 'date'],
        ]);

        $page = Page::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'layout' => $request->layout,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'published_at' => $request->status === 'published' ? ($request->published_at ?? now()) : null,
        ]);

        if ($request->hasFile('featured_image')) {
            $page->addMedia($request->file('featured_image'))->toMediaCollection('page_images');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($page)
            ->log('created page');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        $layouts = $this->getAvailableLayouts();
        return view('admin.pages.edit', compact('page', 'layouts'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages')->ignore($page->id)],
            'content' => ['nullable', 'string'],
            'layout' => ['required', 'string', 'in:default,full-width,sidebar-left,sidebar-right'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft,archived'],
            'published_at' => ['nullable', 'date'],
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'layout' => $request->layout,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
            'updated_by' => auth()->id(),
            'published_at' => $request->status === 'published' ? ($request->published_at ?? $page->published_at ?? now()) : null,
        ]);

        if ($request->hasFile('featured_image')) {
            $page->clearMediaCollection('page_images');
            $page->addMedia($request->file('featured_image'))->toMediaCollection('page_images');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($page)
            ->log('updated page');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($page)
            ->log('deleted page');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:delete,publish,draft,archive'],
            'ids' => ['required', 'array'],
        ]);

        $pages = Page::whereIn('id', $request->ids)->get();

        foreach ($pages as $page) {
            switch ($request->action) {
                case 'delete':
                    $page->delete();
                    break;
                case 'publish':
                    $page->update(['status' => 'published', 'published_at' => now()]);
                    break;
                case 'draft':
                    $page->update(['status' => 'draft']);
                    break;
                case 'archive':
                    $page->update(['status' => 'archived']);
                    break;
            }
        }

        return redirect()->back()->with('success', 'Bulk action completed successfully.');
    }

    private function getAvailableLayouts()
    {
        return [
            'default' => 'Default Layout',
            'full-width' => 'Full Width',
            'sidebar-left' => 'Sidebar Left',
            'sidebar-right' => 'Sidebar Right',
        ];
    }
}