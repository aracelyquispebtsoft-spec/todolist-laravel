<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // withCount evita el problema N+1: una sola consulta trae el número de
        // tareas de cada categoría, en lugar de una consulta por fila.
        $categories = Category::withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $category = new Category;
        $category->name = $datos['name'];
        $category->save();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category->load('tasks');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $datos = $request->validated();

        $category->name = $datos['name'];
        $category->save();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * Las tareas de la categoría no se borran: la clave foránea está definida
     * con nullOnDelete, así que quedan sin categoría.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
