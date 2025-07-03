<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class ServicesMarketplaceController extends Controller
{
    /**
     * Muestra la página principal del marketplace con servicios filtrables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Filtro por búsqueda de nombre o descripción del servicio
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filtro por categoría
        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        // ¡IMPORTANTE! Eager load las relaciones necesarias:
        // - 'category' para mostrar el nombre de la categoría del servicio.
        // - 'serviceProvider.user' para el nombre del proveedor.
        // - 'serviceProvider.categories' para las categorías que ofrece el proveedor.
        // - 'serviceProvider' para los campos directos del perfil del proveedor (bio, areas, etc.).
        $services = $query->with([
            'category',
            'serviceProvider.user',
            'serviceProvider.categories' // Cargar las categorías asociadas al ServiceProvider
        ])->paginate(9); // Paginación para no cargar todos los servicios a la vez

        $categories = Category::all(); // Todas las categorías para el filtro

        return view('marketplace.services.index', compact('services', 'categories'));
    }

    /**
     * Muestra los detalles de un servicio específico.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function show(Service $service)
    {
        // Eager load las mismas relaciones que en el index para la vista de detalle
        $service->load([
            'category',
            'serviceProvider.user',
            'serviceProvider.categories'
        ]);

        return view('marketplace.services.show', compact('service'));
    }
}
