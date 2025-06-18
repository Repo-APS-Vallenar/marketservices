<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());
        return response()->json(['message' => 'Categoría creada exitosamente', 'category' => $category], 201);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());
        return response()->json(['message' => 'Categoría actualizada exitosamente', 'category' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Categoría eliminada exitosamente']);
    }
} 