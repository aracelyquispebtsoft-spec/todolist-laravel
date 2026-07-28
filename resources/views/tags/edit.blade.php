@extends('layout')

@section('title', 'Editar etiqueta')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Editar etiqueta</h1>

    <form action="{{ route('tags.update', $tag) }}" method="POST" class="mt-6 max-w-lg">
        @csrf
        @method('PUT')

        @include('tags._form')
    </form>
@endsection
