<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'To-Do List')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">

<header class="bg-slate-900 text-white">
    <div class="mx-auto max-w-5xl px-4 py-4 sm:flex sm:items-center sm:justify-between">
        <a href="{{ url('/') }}" class="block text-lg font-semibold tracking-tight">
            To-Do List
        </a>

        <nav class="mt-3 flex flex-wrap gap-2 sm:mt-0" aria-label="Secciones">
            @foreach ([
                'tasks' => 'Tareas',
                'categories' => 'Categorías',
                'tags' => 'Etiquetas',
            ] as $recurso => $etiqueta)
                {{-- Cada enlace aparece cuando su CRUD entra en el proyecto --}}
                @if (Route::has($recurso . '.index'))
                    <a href="{{ route($recurso . '.index') }}"
                       @class([
                           'rounded-md px-3 py-1.5 text-sm font-medium transition',
                           'bg-white text-slate-900' => request()->routeIs($recurso . '.*'),
                           'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs($recurso . '.*'),
                       ])
                       @if (request()->routeIs($recurso . '.*')) aria-current="page" @endif>
                        {{ $etiqueta }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>
</header>

<main class="mx-auto max-w-5xl px-4 py-8">
    @if (session('success'))
        <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
             role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
             role="alert">
            <p class="font-medium">Revisa los datos del formulario:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
