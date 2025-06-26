<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class ServicesMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()->with(['category', 'serviceProvider.user']);
        
        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Búsqueda por nombre o descripción
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(12);
        $categories = Category::all();

        return view('marketplace.services.index', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        $service->load(['category', 'serviceProvider.user']);
        return view('marketplace.services.show', compact('service'));
    }
}