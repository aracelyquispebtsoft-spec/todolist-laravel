<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // withCount evita el problema N+1: una sola consulta trae el número de
        // tareas de cada etiqueta, en lugar de una consulta por fila.
        $tags = Tag::withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $tag = new Tag;
        $tag->name = $datos['name'];
        $tag->save();

        return redirect()
            ->route('tags.index')
            ->with('success', 'Etiqueta creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): View
    {
        $tag->load('tasks');

        return view('tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagRequest $request, Tag $tag): RedirectResponse
    {
        $datos = $request->validated();

        $tag->name = $datos['name'];
        $tag->save();

        return redirect()
            ->route('tags.index')
            ->with('success', 'Etiqueta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * Las tareas no se borran, pero sí sus filas en la tabla pivote: la clave
     * foránea de tag_task está definida con cascadeOnDelete, así que la etiqueta
     * desaparece de todas las tareas que la tenían.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('success', 'Etiqueta eliminada correctamente.');
    }
}
