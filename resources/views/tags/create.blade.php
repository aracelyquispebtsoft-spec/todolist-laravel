@extends('layout')

@section('title', 'Nueva etiqueta')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Nueva etiqueta</h1>

    <form action="{{ route('tags.store') }}" method="POST" class="mt-6 max-w-lg">
        @csrf

        @include('tags._form', ['tag' => $tag ?? new \App\Models\Tag])
    </form>
@endsection
