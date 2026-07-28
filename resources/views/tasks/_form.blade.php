{{--
    Formulario compartido por crear y editar.
    Espera $task (instancia nueva o existente), $categories y $tags.
--}}
<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Título</label>

            <input type="text" name="title" id="title" required maxlength="255"
                   value="{{ old('title', $task->title) }}"
                   @class([
                       'mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2',
                       'border-red-300 focus:border-red-500 focus:ring-red-200' => $errors->has('title'),
                       'border-slate-300 focus:border-slate-500 focus:ring-slate-200' => ! $errors->has('title'),
                   ])>

            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5">
            <label for="description" class="block text-sm font-medium text-slate-700">
                Descripción <span class="font-normal text-slate-400">(opcional)</span>
            </label>

            <textarea name="description" id="description" rows="4"
                      class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">{{ old('description', $task->description) }}</textarea>

            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5">
            <label for="category_id" class="block text-sm font-medium text-slate-700">Categoría</label>

            <select name="category_id" id="category_id"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                <option value="">— Sin categoría —</option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                            @selected(old('category_id', $task->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <fieldset>
            <legend class="text-sm font-medium text-slate-700">Etiquetas</legend>

            @if ($tags->isEmpty())
                <p class="mt-2 text-sm text-slate-500">
                    No hay etiquetas todavía.
                    <a href="{{ route('tags.create') }}" class="font-medium text-slate-900 underline">Crea una</a>.
                </p>
            @else
                @php
                    $seleccionadas = old('tags', $task->tags->pluck('id')->all());
                @endphp

                <div class="mt-3 flex flex-wrap gap-x-6 gap-y-3">
                    @foreach ($tags as $tag)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   @checked(in_array($tag->id, $seleccionadas))
                                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            @endif

            @error('tags.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </fieldset>

        <div class="mt-6 border-t border-slate-100 pt-5">
            {{-- El campo oculto envía 0 cuando la casilla queda desmarcada, para que
                 is_completed llegue siempre y validated() lo incluya. --}}
            <input type="hidden" name="is_completed" value="0">

            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="is_completed" value="1"
                       @checked(old('is_completed', $task->is_completed))
                       class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                Marcar como completada
            </label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
            Guardar
        </button>

        <a href="{{ route('tasks.index') }}"
           class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancelar</a>
    </div>
</div>
