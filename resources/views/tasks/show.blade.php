@extends('layout')

@section('title', $task->title)

@section('content')
    <div class="sm:flex sm:items-start sm:justify-between">
        <div>
            <span @class([
                'inline-block rounded-full px-2 py-0.5 text-xs font-medium',
                'bg-emerald-100 text-emerald-800' => $task->is_completed,
                'bg-amber-100 text-amber-800' => ! $task->is_completed,
            ])>
                {{ $task->is_completed ? 'Completada' : 'Pendiente' }}
            </span>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $task->title }}</h1>
        </div>

        <div class="mt-3 flex shrink-0 gap-3 sm:mt-0">
            <a href="{{ route('tasks.edit', $task) }}"
               class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                Editar
            </a>

            <a href="{{ route('tasks.index') }}"
               class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                Volver
            </a>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <dl class="space-y-5">
            <div>
                <dt class="text-sm font-semibold uppercase tracking-wide text-slate-500">Descripción</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-slate-800">
                    {{ $task->description ?: 'Sin descripción.' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-semibold uppercase tracking-wide text-slate-500">Categoría</dt>
                <dd class="mt-1 text-sm text-slate-800">
                    @if ($task->category)
                        <a href="{{ route('categories.show', $task->category) }}" class="underline hover:text-slate-950">
                            {{ $task->category->name }}
                        </a>
                    @else
                        <span class="text-slate-500">Sin categoría.</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-sm font-semibold uppercase tracking-wide text-slate-500">Etiquetas</dt>
                <dd class="mt-2">
                    @forelse ($task->tags as $tag)
                        <a href="{{ route('tags.show', $tag) }}"
                           class="mr-1 inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 hover:bg-slate-200">
                            {{ $tag->name }}
                        </a>
                    @empty
                        <span class="text-sm text-slate-500">Sin etiquetas.</span>
                    @endforelse
                </dd>
            </div>
        </dl>
    </div>
@endsection
