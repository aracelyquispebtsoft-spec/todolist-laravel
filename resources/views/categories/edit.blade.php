@extends('layout')

@section('title', 'Editar categoría')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Editar categoría</h1>

    <form action="{{ route('categories.update', $category) }}" method="POST" class="mt-6 max-w-lg">
        @csrf
        @method('PUT')

        @include('categories._form')
    </form>
@endsection
