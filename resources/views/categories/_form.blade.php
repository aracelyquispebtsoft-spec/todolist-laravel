{{--
    Formulario compartido por crear y editar.
    Espera $category (una instancia nueva o existente) y $accion con la ruta destino.
--}}
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Nombre</label>

        <input type="text" name="name" id="name" required maxlength="100"
               value="{{ old('name', $category->name) }}"
               @class([
                   'mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2',
                   'border-red-300 focus:border-red-500 focus:ring-red-200' => $errors->has('name'),
                   'border-slate-300 focus:border-slate-500 focus:ring-slate-200' => ! $errors->has('name'),
               ])>

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
            Guardar
        </button>

        <a href="{{ route('categories.index') }}"
           class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancelar</a>
    </div>
</div>
