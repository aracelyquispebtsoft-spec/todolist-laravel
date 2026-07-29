@extends('layout')

@section('title', 'Nueva categoría')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Nueva categoría</h1>

    <form action="{{ route('categories.store') }}" method="POST" class="mt-6 max-w-lg">
        @csrf

        @include('categories._form', ['category' => $category ?? new \App\Models\Category])
    </form>
@endsection
