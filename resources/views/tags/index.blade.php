@extends('layout')

@section('title', 'Etiquetas')

@section('content')
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Etiquetas</h1>

        <a href="{{ route('tags.create') }}"
           class="mt-3 inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 sm:mt-0">
            Nueva etiqueta
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Tareas</th>
                    <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tags as $tag)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $tag->tasks_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('tags.show', $tag) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Ver</a>

                                <a href="{{ route('tags.edit', $tag) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Editar</a>

                                <form action="{{ route('tags.destroy', $tag) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la etiqueta «{{ $tag->name }}»? Se quitará de las tareas que la tengan.')">
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
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                            Todavía no hay etiquetas.
                            <a href="{{ route('tags.create') }}" class="font-medium text-slate-900 underline">
                                Crea la primera
                            </a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
