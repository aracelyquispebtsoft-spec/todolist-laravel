@extends('layout')

@section('title', 'Nueva tarea')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Nueva tarea</h1>

    <form action="{{ route('tasks.store') }}" method="POST" class="mt-6 max-w-2xl">
        @csrf

        @include('tasks._form')
    </form>
@endsection
