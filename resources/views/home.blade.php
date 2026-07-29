@extends('layout')

@section('title', 'To-Do List')

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
            Gestor de tareas
        </h1>

        <p class="mt-3 max-w-prose text-slate-600">
            Registra tus tareas, organízalas por categoría y clasifícalas con etiquetas.
            Usa el menú superior para moverte entre las secciones.
        </p>
    </div>
@endsection
