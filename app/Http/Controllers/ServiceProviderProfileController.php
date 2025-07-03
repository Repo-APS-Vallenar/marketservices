<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceProviderProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'service_provider') {
                abort(403, 'Acceso no autorizado. Solo los proveedores de servicios pueden gestionar su perfil.');
            }
            return $next($request);
        });
    }

    /**
     * Muestra el formulario de edición del perfil del proveedor de servicios.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $serviceProvider = Auth::user()->serviceProvider;

        if (!$serviceProvider) {
            $serviceProvider = ServiceProvider::create(['user_id' => Auth::id()]);
        }

        // Cargar las categorías asociadas al proveedor para asegurar que estén disponibles
        $serviceProvider->load('categories'); // ¡NUEVA LÍNEA CLAVE AQUÍ!

        // Obtener todas las categorías disponibles para el dropdown
        $allCategories = Category::all();
        // Obtener solo los IDs de las categorías que el proveedor ya ofrece
        $providerCategoriesIds = $serviceProvider->categories->pluck('id')->toArray();

        return view('provider.profile.edit', compact('serviceProvider', 'allCategories', 'providerCategoriesIds'));
    }

    /**
     * Actualiza el perfil del proveedor de servicios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $serviceProvider = Auth::user()->serviceProvider;

        $rules = [
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'service_areas' => 'nullable|string|max:255',
            'certification' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'categories' => 'nullable|array', // Las categorías ahora son un array de IDs
            'categories.*' => 'exists:categories,id', // Cada ID debe existir en la tabla categories
        ];

        $validatedData = $request->validate($rules);

        // Manejar la subida de la imagen de perfil
        if ($request->hasFile('profile_picture')) {
            // Eliminar la imagen antigua si existe
            if ($serviceProvider->profile_picture) {
                Storage::delete($serviceProvider->profile_picture);
            }
            // Guardar la nueva imagen y obtener su ruta
            $path = $request->file('profile_picture')->store('public/profile_pictures');
            $serviceProvider->profile_picture = $path;
        }

        // Actualizar los campos del perfil
        $serviceProvider->phone_number = $validatedData['phone_number'] ?? null;
        $serviceProvider->bio = $validatedData['bio'] ?? null;
        $serviceProvider->service_areas = $validatedData['service_areas'] ?? null;
        $serviceProvider->certification = $validatedData['certification'] ?? null;

        $serviceProvider->save();

        // Sincronizar las categorías seleccionadas
        if (isset($validatedData['categories'])) {
            $serviceProvider->categories()->sync($validatedData['categories']);
        } else {
            $serviceProvider->categories()->detach(); // Si no se selecciona ninguna, desasocia todas
        }

        return redirect()->route('provider.profile.edit')->with('success', 'Perfil de proveedor actualizado exitosamente.');
    }
}
