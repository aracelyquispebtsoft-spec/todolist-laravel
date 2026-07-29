@extends('layout')

@section('title', $tag->name)

@section('content')
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $tag->name }}</h1>

        <div class="mt-3 flex gap-3 sm:mt-0">
            <a href="{{ route('tags.edit', $tag) }}"
               class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                Editar
            </a>

            <a href="{{ route('tags.index') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                Volver
            </a>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
            Tareas con esta etiqueta ({{ $tag->tasks->count() }})
        </h2>

        @if ($tag->tasks->isEmpty())
            <p class="mt-3 text-sm text-slate-500">Ninguna tarea usa esta etiqueta.</p>
        @else
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($tag->tasks as $task)
                    <li class="flex items-center gap-3 py-2 text-sm">
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            'bg-emerald-100 text-emerald-800' => $task->is_completed,
                            'bg-slate-100 text-slate-600' => ! $task->is_completed,
                        ])>
                            {{ $task->is_completed ? 'Completada' : 'Pendiente' }}
                        </span>

                        <span class="text-slate-800">{{ $task->title }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
