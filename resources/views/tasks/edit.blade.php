@extends('layout')

@section('title', 'Editar tarea')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Editar tarea</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST" class="mt-6 max-w-2xl">
        @csrf
        @method('PUT')

        @include('tasks._form')
    </form>
@endsection
