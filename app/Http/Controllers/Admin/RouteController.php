<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as LaravelRoute;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    public function index()
    {
        $routes = CustomRoute::ordered()->paginate(10);
        $controllers = $this->getAvailableControllers();
        return view('admin.routes.index', compact('routes', 'controllers'));
    }

    public function create()
    {
        $controllers = $this->getAvailableControllers();
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        $middleware = $this->getAvailableMiddleware();
        
        return view('admin.routes.create', compact('controllers', 'methods', 'middleware'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255', 'unique:custom_routes,path'],
            'controller' => ['required', 'string'],
            'method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE'],
            'middleware' => ['nullable', 'array'],
            'middleware.*' => ['string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $route = CustomRoute::create([
            'name' => $request->name,
            'path' => $request->path,
            'controller' => $request->controller,
            'method' => $request->method,
            'middleware' => $request->middleware ?? [],
            'order' => $request->order ?? 0,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($route)
            ->log('created custom route');

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully.');
    }

    public function edit(CustomRoute $route)
    {
        $controllers = $this->getAvailableControllers();
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        $middleware = $this->getAvailableMiddleware();

        return view('admin.routes.edit', compact('route', 'controllers', 'methods', 'middleware'));
    }

    public function update(Request $request, CustomRoute $route)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255', Rule::unique('custom_routes')->ignore($route->id)],
            'controller' => ['required', 'string'],
            'method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE'],
            'middleware' => ['nullable', 'array'],
            'middleware.*' => ['string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'active' => ['boolean'],
        ]);

        $route->update([
            'name' => $request->name,
            'path' => $request->path,
            'controller' => $request->controller,
            'method' => $request->method,
            'middleware' => $request->middleware ?? [],
            'order' => $request->order ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($route)
            ->log('updated custom route');

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(CustomRoute $route)
    {
        $route->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($route)
            ->log('deleted custom route');

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route deleted successfully.');
    }

    public function toggle(CustomRoute $route)
    {
        $route->update(['active' => !$route->active]);

        $status = $route->active ? 'activated' : 'deactivated';

        activity()
            ->causedBy(auth()->user())
            ->performedOn($route)
            ->log("{$status} custom route");

        return redirect()->back()->with('success', "Route {$status} successfully.");
    }

    private function getAvailableControllers()
    {
        return [
            'App\Http\Controllers\PageController' => 'Page Controller',
            'App\Http\Controllers\HomeController' => 'Home Controller',
            'App\Http\Controllers\ContactController' => 'Contact Controller',
            'App\Http\Controllers\BlogController' => 'Blog Controller',
        ];
    }

    private function getAvailableMiddleware()
    {
        return [
            'web' => 'Web',
            'auth' => 'Authentication',
            'admin' => 'Admin',
            'guest' => 'Guest',
        ];
    }
}