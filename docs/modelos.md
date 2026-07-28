# Modelos del proyecto

Un modelo es una clase que representa una tabla; cada instancia es una fila. Este
proyecto tiene tres: `Category`, `Tag` y `Task`.

```bash
php artisan make:model Task
```

El nombre va en **singular y PascalCase**. Laravel deduce la tabla pasándolo a snake_case
y pluralizándolo: `Task` → `tasks`, `Category` → `categories`, `Tag` → `tags`.

## Persistencia: sin asignación masiva

**Esta es la regla central del proyecto.** La asignación masiva es rellenar varios
atributos de golpe a partir de un arreglo, y aquí **no se usa en absoluto**:

- No se declara `$fillable` ni `$guarded`.
- No se llama a `Model::unguard()`.
- No se usan `create()`, `update()` ni `fill()` con arreglos de datos.
- Toda la validación se hace mediante **Form Requests**.
- Los datos se obtienen **únicamente** con `$request->validated()`.
- Cada atributo se asigna manualmente y se persiste con `save()`.

Al asignar propiedad por propiedad no se pasa por el mecanismo de asignación masiva, así
que Eloquent no lanza `MassAssignmentException` aunque los modelos no declaren nada.

### El flujo obligatorio

```php
public function store(StoreTaskRequest $request): RedirectResponse
{
    // 1 y 2. El Form Request ya validó; validated() devuelve solo lo declarado
    $datos = $request->validated();

    // 3. Crear el modelo
    $task = new Task;

    // 4. Asignar cada propiedad individualmente
    $task->title       = $datos['title'];
    $task->description = $datos['description'] ?? null;
    $task->category_id = $datos['category_id'] ?? null;

    // 5. Persistir
    $task->save();

    $task->tags()->sync($datos['tags'] ?? []);

    return redirect()->route('tasks.index');
}
```

Editar es idéntico salvo que el modelo se recupera en lugar de crearse —normalmente por
*route model binding*, que lo inyecta ya resuelto en `update(UpdateTaskRequest $request,
Task $task)`.

`save()` decide por sí mismo entre `INSERT` y `UPDATE` según el modelo tenga o no clave
primaria.

### Lo que no se debe escribir

```php
// ❌ asignación masiva
Task::create($datos);
$task->update($datos);
$task->fill($datos)->save();

// ❌ saltarse la guarda por la puerta de atrás
Task::forceCreate($datos);
$task->forceFill($datos)->save();

// ❌ datos sin validar, en cualquier forma
$request->all();
$request->input('title');
```

`validated()` devuelve **exclusivamente** los campos declarados en las reglas del Form
Request, así que cualquier campo extra que mande el navegador queda fuera. Es la lista
blanca del proyecto, y por eso vive en el Form Request y no en el modelo.

`sync()` sobre una relación **sí** se usa: no es asignación masiva de atributos, sino la
forma normal de gestionar una tabla pivote. Recibe identificadores, no columnas.

## Relaciones

Se declaran como métodos, y el nombre del método importa porque Eloquent lo usa para
deducir las claves foráneas.

```php
// Task — la tabla tasks tiene la columna category_id
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// Category — busca category_id en la tabla tasks. Método en plural
public function tasks(): HasMany
{
    return $this->hasMany(Task::class);
}

// Task y Tag — se declara en AMBOS modelos, apuntando a la misma pivote
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class);
}
```

**El nombre de la pivote se deduce** ordenando los dos modelos en singular
alfabéticamente y uniéndolos con guion bajo: para `Tag` y `Task` sale **`tag_task`**,
porque en la cuarta letra `g` es menor que `s`. Es fácil equivocarse, así que antes de
escribir la migración conviene preguntárselo al framework:

```bash
php artisan tinker --execute='echo (new App\Models\Task)->tags()->getTable();'
```

### Uso habitual

```php
$task->category->name;          // objeto Category
$task->tags->pluck('name');     // ['urgente', 'revisar']
$category->tasks()->count();    // cuenta sin cargar los registros

$task->tags()->sync([1, 2, 3]); // deja exactamente esas etiquetas
```

`sync()` es el que se usa al guardar un formulario: recibe el listado completo de
etiquetas marcadas, añade las nuevas y elimina las desmarcadas. Es idempotente —
llamarlo dos veces con los mismos identificadores no duplica filas.

## Cargar relaciones: el problema N+1

Este bucle lanza 1 consulta para las tareas y **una más por cada tarea**:

```php
$tasks = Task::all();
foreach ($tasks as $task) {
    echo $task->category->name;   // una consulta por vuelta
}
```

Con 100 tareas son 101 consultas. `with()` las resuelve en 2:

```php
$tasks = Task::with(['category', 'tags'])->get();
```

**Regla práctica: si en una vista vas a recorrer registros y acceder a una relación,
cárgala con `with()` en el controlador.** En este proyecto aplica al listado de tareas,
que muestra la categoría y las etiquetas de cada fila.

## Sin casts

Los modelos se mantienen al mínimo: solo relaciones, sin declarar `casts()`.

La consecuencia es que `is_completed` se lee como **entero**, porque MySQL almacena los
booleanos como `tinyint(1)`. En Blade no cambia nada, pero las comparaciones estrictas no
se cumplen:

```php
$task->is_completed === true    // false aunque la tarea esté completada
if ($task->is_completed) { }    // correcto, y es la forma preferida
```

Al escribir el valor, usa `$request->boolean('is_completed')`, que devuelve un booleano
real. Un checkbox HTML sin `value` envía la cadena `"on"`, y asignarla directamente falla
con `Incorrect integer value: 'on'`.

## Errores frecuentes

| Error | Causa |
| ----- | ----- |
| `Add [title] to fillable property to allow mass assignment` | Se usó `create()`, `update()` o `fill()` con un arreglo: asigna atributo por atributo y llama a `save()` |
| `Table 'x.task_tag' doesn't exist` | El nombre de la pivote no sigue la convención alfabética: es `tag_task` |
| `Call to a member function name on null` | La relación es `null`: usa `$task->category?->name` |
| La página va lentísima al listar | Falta `with()`: problema N+1 |
