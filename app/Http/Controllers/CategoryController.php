<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden acceder a este controlador
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Acceso no autorizado. Solo los administradores pueden gestionar categorías.');
            }
            return $next($request);
        });
    }

    /**
     * Muestra una lista de todas las categorías.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($validatedData);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Elimina una categoría de la base de datos.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Aquí puedes añadir lógica adicional, por ejemplo, si no se puede eliminar una categoría
        // que tiene servicios asociados. Por ahora, simplemente la elimina.
        try {
            $category->delete();
            return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar error si hay servicios u otras relaciones que impiden la eliminación
            return redirect()->back()->with('error', 'No se puede eliminar la categoría porque tiene elementos asociados (ej. servicios).');
        }
    }
}
