<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // with() carga categoría y etiquetas por adelantado: sin esto el listado
        // haría una consulta por cada tarea para cada relación (problema N+1).
        $tasks = Task::with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create', [
            'task' => new Task,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $task = new Task;
        $task->title = $datos['title'];
        $task->description = $datos['description'] ?? null;
        $task->category_id = $datos['category_id'] ?? null;
        $task->is_completed = $datos['is_completed'];
        $task->save();

        // sync() gestiona la tabla pivote con identificadores; no es asignación
        // masiva de atributos del modelo.
        $task->tags()->sync($datos['tags'] ?? []);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        $task->load(['category', 'tags']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $task->load('tags');

        return view('tasks.edit', [
            'task' => $task,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $datos = $request->validated();

        $task->title = $datos['title'];
        $task->description = $datos['description'] ?? null;
        $task->category_id = $datos['category_id'] ?? null;
        $task->is_completed = $datos['is_completed'];
        $task->save();

        $task->tags()->sync($datos['tags'] ?? []);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Toggle the completion state of the task.
     *
     * No recibe datos del cliente —el estado se deduce invirtiendo el actual—,
     * así que no necesita Form Request. La asignación sigue siendo individual.
     */
    public function toggle(Task $task): RedirectResponse
    {
        $task->is_completed = ! $task->is_completed;
        $task->save();

        $mensaje = $task->is_completed
            ? 'Tarea marcada como completada.'
            : 'Tarea marcada como pendiente.';

        // back() devuelve al listado conservando la página y los filtros.
        return back()->with('success', $mensaje);
    }

    /**
     * Remove the specified resource from storage.
     *
     * Sus filas en tag_task desaparecen por el cascadeOnDelete de la pivote.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }
}
