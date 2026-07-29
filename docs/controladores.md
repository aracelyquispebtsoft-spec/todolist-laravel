# Controladores del proyecto

Un controlador recibe la petición, decide qué hacer y devuelve una respuesta: una vista
o una redirección. Este proyecto tiene tres, uno por recurso: `CategoryController`,
`TagController` y `TaskController`.

## Crear el archivo

```bash
php artisan make:controller TaskController --resource --model=Task
```

| Flag | Qué aporta |
| ---- | ---------- |
| `--resource` | Genera los siete métodos del CRUD ya esbozados |
| `--model=Task` | Inyecta el modelo como parámetro en `show`, `edit`, `update` y `destroy` |

Sin `--model` los métodos reciben un `$id` suelto y tendrías que buscar el registro a
mano en cada uno.

## Los siete métodos

| Método | Verbo y URL | Qué hace |
| ------ | ----------- | -------- |
| `index` | GET `/tasks` | Lista los registros |
| `create` | GET `/tasks/create` | Muestra el formulario de alta |
| `store` | POST `/tasks` | Guarda el nuevo registro |
| `show` | GET `/tasks/{task}` | Muestra un registro |
| `edit` | GET `/tasks/{task}/edit` | Muestra el formulario de edición |
| `update` | PUT `/tasks/{task}` | Guarda los cambios |
| `destroy` | DELETE `/tasks/{task}` | Elimina el registro |

Los siete se registran con una sola línea:

```php
Route::resource('tasks', TaskController::class);
```

Comprueba lo que queda registrado con `php artisan route:list --name=tasks`.

## Route model binding

Al declarar el tipo del parámetro, Laravel busca el registro por su identificador y lo
inyecta ya resuelto. Si no existe, devuelve un 404 automáticamente.

```php
public function show(Task $task): View   // ← llega el modelo, no el id
{
    return view('tasks.show', compact('task'));
}
```

El nombre del parámetro **debe coincidir** con el de la ruta (`{task}` → `$task`). Si no
coinciden, llega `null` y el error aparece más tarde, al usarlo en la vista.

## Validación: Form Requests

La validación **no va en el controlador**. Se declara en una clase aparte que se inyecta
como parámetro; si falla, Laravel redirige al formulario con los errores antes de que el
método llegue a ejecutarse.

```bash
php artisan make:request TaskRequest
```

```php
class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // el proyecto no maneja autenticación
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_completed' => ['required', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return ['title.required' => 'El título es obligatorio.'];
    }
}
```

`authorize()` viene con `return false` de fábrica: si lo dejas así, **toda petición
recibe un 403** y el formulario parece no responder.

`messages()` existe porque Laravel no incluye traducciones al español. Sin él verías
mezclas como «The title field is required.» en una interfaz en castellano.

Un mismo Form Request sirve para `store` y `update`. Cuando la regla depende del
registro que se edita, se consulta la ruta:

```php
Rule::unique('categories', 'name')->ignore($this->route('category'))
```

Sin ese `ignore`, editar una categoría sin cambiarle el nombre chocaría contra sí misma.

## Persistencia: asignación individual

Este proyecto no usa asignación masiva. El flujo es siempre el mismo:

```php
public function store(TaskRequest $request): RedirectResponse
{
    $datos = $request->validated();      // solo los campos de rules()

    $task = new Task;                    // o Task recibido por binding, al editar
    $task->title = $datos['title'];
    $task->description = $datos['description'] ?? null;
    $task->category_id = $datos['category_id'] ?? null;
    $task->is_completed = $datos['is_completed'];
    $task->save();

    $task->tags()->sync($datos['tags'] ?? []);

    return redirect()
        ->route('tasks.index')
        ->with('success', 'Tarea creada correctamente.');
}
```

`update()` es idéntico salvo que el modelo llega por *route model binding* en lugar de
instanciarse. Los detalles y lo que está prohibido están en [modelos.md](modelos.md).

El `?? null` importa: los campos opcionales que llegan vacíos no aparecen en
`validated()`, y sin el valor por defecto tendrías un error de índice indefinido.

## Redirecciones y mensajes

Después de guardar **siempre se redirige**, nunca se devuelve una vista. Si devolvieras
la vista directamente, recargar la página reenviaría el formulario y duplicaría el
registro.

```php
return redirect()->route('tasks.index')->with('success', 'Tarea creada correctamente.');

return back()->with('success', 'Tarea marcada como completada.');
```

`with()` guarda el mensaje en sesión para una única petición. El layout lo recoge con
`session('success')`, así que no hay que repetirlo en cada vista.

`back()` vuelve a la página anterior, útil en acciones que se lanzan desde el propio
listado y deben devolver al usuario justo donde estaba.

## Cargar relaciones

Si la vista recorre registros y accede a relaciones, cárgalas por adelantado o tendrás
una consulta por fila:

```php
// listado de tareas: trae categoría y etiquetas en 3 consultas, no en 21
$tasks = Task::with(['category', 'tags'])->latest()->get();

// listado de categorías: solo el número de tareas, sin traerlas
$categories = Category::withCount('tasks')->orderBy('name')->get();
```

`withCount('tasks')` deja disponible `$category->tasks_count` sin cargar las tareas.

Los listados devuelven todos los registros con `get()`. El proyecto no usa paginación:
con el volumen que maneja no aporta nada y complica las vistas. Si en algún momento
hiciera falta, se cambia `get()` por `paginate(10)` y se añade `{{ $tasks->links() }}`
al final de la tabla.

## Métodos fuera del CRUD

Cuando una acción no encaja en los siete, se añade su propio método y su propia ruta.
Este proyecto tiene uno para alternar el estado de una tarea:

```php
public function toggle(Task $task): RedirectResponse
{
    $task->is_completed = ! $task->is_completed;
    $task->save();

    return back()->with('success', 'Estado actualizado.');
}
```

```php
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
Route::resource('tasks', TaskController::class);
```

No recibe datos del cliente —el nuevo estado se deduce invirtiendo el actual—, así que
no necesita Form Request. Se usa `PATCH` porque modifica un solo campo, no el recurso
entero.

## Errores frecuentes

| Error | Causa |
| ----- | ----- |
| `403 This action is unauthorized` | `authorize()` sigue devolviendo `false` en el Form Request |
| `Undefined array key "description"` | El campo opcional no llegó: usa `$datos['description'] ?? null` |
| `View [tasks.index] not found` | Falta la vista o el nombre no coincide con la carpeta |
| `Route [tasks.index] not defined` | Falta el `Route::resource`, o hay una errata en el nombre |
| El formulario no borra un valor | Faltó enviar el campo: revisa el `@method('PUT')` y los campos ocultos |
| El listado va lentísimo | Falta `with()`: una consulta por fila (problema N+1) |
| Al recargar se duplica el registro | Se devolvió una vista en vez de redirigir tras guardar |
