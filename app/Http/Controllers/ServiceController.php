<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    // Mostrar listado de servicios del proveedor autenticado
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $services = Service::with('category')->get();
        } else {
            $provider = $user->serviceProvider;
            if (!$provider) {
                abort(403, 'No autorizado');
            }
            $services = $provider->services()->with('category')->get();
        }
        $categories = \App\Models\Category::all();
        return view('provider.services.index', compact('services', 'categories'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();
        if ($user->role === 'admin' || ($user->role === 'service_provider' && $user->serviceProvider)) {
            return view('provider.services.create', compact('categories'));
        }
        abort(403, 'No autorizado');
    }

    // Guardar nuevo servicio
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'estimated_duration' => 'nullable|integer|min:1',
                'service_provider_id' => 'required|exists:service_providers,id',
            ]);
            Service::create($validated);
            return redirect()->route('services.index')->with('success', 'Servicio creado exitosamente');
        } elseif ($user->role === 'service_provider' && $user->serviceProvider) {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'estimated_duration' => 'nullable|integer|min:1',
            ]);
            $user->serviceProvider->services()->create($validated);
            return redirect()->route('services.index')->with('success', 'Servicio creado exitosamente');
        }
        abort(403, 'No autorizado');
    }

    // Mostrar formulario de edición
    public function edit(Service $service)
    {
        $user = Auth::user();
        $categories = Category::all();
        if ($user->role === 'admin' || ($user->role === 'service_provider' && $service->service_provider_id === optional($user->serviceProvider)->id)) {
            return view('provider.services.edit', compact('service', 'categories'));
        }
        abort(403, 'No autorizado');
    }

    // Actualizar servicio
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'estimated_duration' => 'nullable|integer|min:1',
                'service_provider_id' => 'required|exists:service_providers,id',
            ]);
            $service->update($validated);
            return redirect()->route('services.index')->with('success', 'Servicio actualizado exitosamente');
        } elseif ($user->role === 'service_provider' && $service->service_provider_id === optional($user->serviceProvider)->id) {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'estimated_duration' => 'nullable|integer|min:1',
            ]);
            $service->update($validated);
            return redirect()->route('services.index')->with('success', 'Servicio actualizado exitosamente');
        }
        abort(403, 'No autorizado');
    }

    // Eliminar servicio
    public function destroy(Service $service)
    {
        $user = Auth::user();
        if ($user->role === 'admin' || ($user->role === 'service_provider' && $service->service_provider_id === optional($user->serviceProvider)->id)) {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Servicio eliminado exitosamente');
        }
        abort(403, 'No autorizado');
    }
} 