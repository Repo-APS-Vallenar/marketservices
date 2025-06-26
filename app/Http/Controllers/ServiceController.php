<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceProvider; // Asegúrate de importar ServiceProvider

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function checkAuthorization()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['service_provider', 'admin'])) {
            abort(403, 'No tienes permisos suficientes para acceder a esta sección.');
        }
    }

    /**
     * Muestra una lista de los servicios.
     * Los administradores ven todos los servicios, los proveedores ven solo los suyos.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $this->checkAuthorization();

        $user = Auth::user();
        $services = collect(); 
        $serviceProviders = collect();

        if ($user->role === 'admin') {
            // Admin ve todos los servicios, cargando proveedor y su usuario
            $services = Service::with('category', 'serviceProvider.user')->get(); 
            $serviceProviders = ServiceProvider::all(); // También necesitamos todos los ServiceProviders para el select en el modal
        } elseif ($user->role === 'service_provider') {
            $provider = $user->serviceProvider;
            if (!$provider) {
                return redirect()->back()->with('error', 'Tu perfil de proveedor no está configurado. Por favor, completa tu perfil de proveedor.');
            }
            // Proveedor de servicios ve solo sus servicios, cargando categoría y el usuario del proveedor
            $services = Service::where('service_provider_id', $provider->id)
                                ->with('category', 'serviceProvider.user') // Cargar serviceProvider.user para todos los servicios
                                ->get();
        }
        
        $categories = Category::all();
        // dd($services->toArray()); // <--- DESCOMENTA ESTA LÍNEA TEMPORALMENTE PARA DEPURAR
        return view('provider.services.index', compact('services', 'categories', 'serviceProviders')); 
    }

    public function create()
    {
        $this->checkAuthorization();

        $user = Auth::user();
        $categories = collect(); 
        $serviceProviders = collect();

        if ($user->role === 'admin') {
            $categories = Category::all();
            $serviceProviders = ServiceProvider::all();
            return view('provider.services.create', compact('categories', 'serviceProviders'));
        } elseif ($user->role === 'service_provider' && $user->serviceProvider) {
            $categories = $user->serviceProvider->categories;
        } else {
             abort(403, 'No autorizado');
        }
        
        return view('provider.services.create', compact('categories', 'serviceProviders'));
    }

    public function store(Request $request)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'estimated_duration' => 'nullable|integer|min:1',
        ];

        if ($user->role === 'admin') {
            $rules['service_provider_id'] = 'required|exists:service_providers,id';
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }
        
        if ($user->role === 'admin') {
            Service::create($validated);
        } elseif ($user->role === 'service_provider') {
            if (!$user->serviceProvider) {
                abort(403, 'Tu perfil de proveedor no está configurado.');
            }
            
            $requestedCategoryId = $validated['category_id'];
            if (!$user->serviceProvider->categories()->where('category_id', $requestedCategoryId)->exists()) {
                return redirect()->back()->withErrors(['category_id' => 'La categoría seleccionada no está asociada a tu perfil de proveedor.'])->withInput();
            }

            $validated['service_provider_id'] = $user->serviceProvider->id;
            
            try {
                Service::create($validated);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Hubo un problema al guardar el servicio. Inténtalo de nuevo.')->withInput();
            }
        } else {
            abort(403, 'No autorizado para crear servicios.');
        }
        
        return redirect()->route('services.index')->with('success', 'Servicio creado exitosamente');
    }

    public function edit(Service $service)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        $categories = collect(); 
        $serviceProviders = collect();

        if ($user->role === 'admin') {
            $serviceProviders = ServiceProvider::all();
            $categories = Category::all();
            return view('provider.services.edit', compact('service', 'categories', 'serviceProviders'));
        } elseif ($user->role === 'service_provider') {
            if ($service->service_provider_id !== optional($user->serviceProvider)->id) {
                abort(403, 'No estás autorizado para editar este servicio.');
            }
            $categories = $user->serviceProvider->categories;
            return view('provider.services.edit', compact('service', 'categories'));
        }
        abort(403, 'No autorizado');
    }

    public function update(Request $request, Service $service)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'estimated_duration' => 'nullable|integer|min:1',
        ];

        if ($user->role === 'admin') {
            $rules['service_provider_id'] = 'required|exists:service_providers,id';
        } else {
            if ($service->service_provider_id !== optional($user->serviceProvider)->id) {
                abort(403, 'No estás autorizado para actualizar este servicio.');
            }
        }

        $validated = $request->validate($rules);

        if ($user->role === 'admin') {
            $service->update($validated);
        } elseif ($user->role === 'service_provider') {
            $requestedCategoryId = $validated['category_id'];
            if (!$user->serviceProvider->categories()->where('category_id', $requestedCategoryId)->exists()) {
                return redirect()->back()->withErrors(['category_id' => 'La categoría seleccionada no está asociada a tu perfil de proveedor.'])->withInput();
            }
            
            unset($validated['service_provider_id']); 
            $service->update($validated);
        } else {
            abort(403, 'No autorizado para actualizar servicios.');
        }
        
        return redirect()->route('services.index')->with('success', 'Servicio actualizado exitosamente');
    }

    public function destroy(Service $service)
    {
        $this->checkAuthorization();
        
        $user = Auth::user();
        if ($user->role === 'admin' || ($user->role === 'service_provider' && $service->service_provider_id === optional($user->serviceProvider)->id)) {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Servicio eliminado exitosamente');
        }
        abort(403, 'No autorizado');
    }
}
