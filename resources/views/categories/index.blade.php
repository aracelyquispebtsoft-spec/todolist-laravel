@extends('layout')

@section('title', 'Categorías')

@section('content')
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Categorías</h1>

        <a href="{{ route('categories.create') }}"
           class="mt-3 inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 sm:mt-0">
            Nueva categoría
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
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $category->tasks_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('categories.show', $category) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Ver</a>

                                <a href="{{ route('categories.edit', $category) }}"
                                   class="font-medium text-slate-600 hover:text-slate-900">Editar</a>

                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la categoría «{{ $category->name }}»? Sus tareas quedarán sin categoría.')">
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
                            Todavía no hay categorías.
                            <a href="{{ route('categories.create') }}" class="font-medium text-slate-900 underline">
                                Crea la primera
                            </a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif
@endsection
