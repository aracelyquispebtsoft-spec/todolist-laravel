@extends('layout')

@section('title', 'Tareas')

@section('content')
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Tareas</h1>

        <a href="{{ route('tasks.create') }}"
           class="mt-3 inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 sm:mt-0">
            Nueva tarea
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Estado</th>
                    <th scope="col" class="px-4 py-3">Título</th>
                    <th scope="col" class="px-4 py-3">Categoría</th>
                    <th scope="col" class="px-4 py-3">Etiquetas</th>
                    <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tasks as $task)
                    <tr>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-block rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap',
                                'bg-emerald-100 text-emerald-800' => $task->is_completed,
                                'bg-amber-100 text-amber-800' => ! $task->is_completed,
                            ])>
                                {{ $task->is_completed ? 'Completada' : 'Pendiente' }}
                            </span>
                        </td>

                        <td @class([
                            'px-4 py-3 font-medium',
                            'text-slate-400 line-through' => $task->is_completed,
                            'text-slate-900' => ! $task->is_completed,
                        ])>
                            {{ $task->title }}
                        </td>

                        <td class="px-4 py-3 text-slate-600">
                            {{ $task->category?->name ?? '—' }}
                        </td>

                        <td class="px-4 py-3">
                            @forelse ($task->tags as $tag)
                                <span class="mr-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                    {{ $tag->name }}
                                </span>
                            @empty
                                <span class="text-slate-400">—</span>
                            @endforelse
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('tasks.show', $task) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Ver</a>

                                <a href="{{ route('tasks.edit', $task) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Editar</a>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la tarea «{{ $task->title }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            Todavía no hay tareas.
                            <a href="{{ route('tasks.create') }}" class="font-medium text-slate-900 underline">
                                Crea la primera
                            </a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tasks->hasPages())
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    @endif
@endsection
