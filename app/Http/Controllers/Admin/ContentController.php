<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    public function index()
    {
        $contents = Content::with('creator')->latest()->paginate(10);
        return view('admin.contents.index', compact('contents'));
    }

    public function create()
    {
        $types = $this->getContentTypes();
        $locations = $this->getAvailableLocations();
        return view('admin.contents.create', compact('types', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:contents,slug'],
            'type' => ['required', 'string', Rule::in(array_keys($this->getContentTypes()))],
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'settings' => ['nullable', 'array'],
        ]);

        $content = Content::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'type' => $request->type,
            'content' => $request->content,
            'location' => $request->location,
            'order' => $request->order ?? 0,
            'settings' => $request->settings ?? [],
            'created_by' => auth()->id(),
        ]);

        if ($request->hasFile('featured_image')) {
            $content->addMedia($request->file('featured_image'))->toMediaCollection('content_images');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($content)
            ->log('created content');

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content created successfully.');
    }

    public function show(Content $content)
    {
        return view('admin.contents.show', compact('content'));
    }

    public function edit(Content $content)
    {
        $types = $this->getContentTypes();
        $locations = $this->getAvailableLocations();
        return view('admin.contents.edit', compact('content', 'types', 'locations'));
    }

    public function update(Request $request, Content $content)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('contents')->ignore($content->id)],
            'type' => ['required', 'string', Rule::in(array_keys($this->getContentTypes()))],
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $content->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'type' => $request->type,
            'content' => $request->content,
            'location' => $request->location,
            'order' => $request->order ?? 0,
            'settings' => $request->settings ?? [],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('featured_image')) {
            $content->clearMediaCollection('content_images');
            $content->addMedia($request->file('featured_image'))->toMediaCollection('content_images');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($content)
            ->log('updated content');

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    public function destroy(Content $content)
    {
        $content->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($content)
            ->log('deleted content');

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content deleted successfully.');
    }

    private function getContentTypes()
    {
        return [
            'block' => 'Content Block',
            'section' => 'Section',
            'widget' => 'Widget',
            'banner' => 'Banner',
            'testimonial' => 'Testimonial',
            'faq' => 'FAQ',
        ];
    }

    private function getAvailableLocations()
    {
        return [
            'header' => 'Header',
            'footer' => 'Footer',
            'sidebar' => 'Sidebar',
            'homepage' => 'Homepage',
            'contact' => 'Contact Page',
        ];
    }
}