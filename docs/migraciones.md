# Guía: crear migraciones en Laravel

Una migración es una clase PHP que describe un cambio en el esquema de la base de
datos. En lugar de escribir SQL a mano y perder el rastro de quién cambió qué, el
esquema queda versionado en el repositorio junto al código.

## Crear el archivo

Nunca se crea a mano: se genera con Artisan, que produce el archivo con el timestamp
y la estructura correctos.

```bash
php artisan make:migration create_categories_table --create=categories
```

| Flag | Para qué sirve |
| ---- | -------------- |
| `--create=tabla` | Genera el esqueleto de una tabla nueva |
| `--table=tabla`  | Genera el esqueleto para modificar una tabla existente |

La convención del nombre importa: `create_<tabla>_table` para crear,
`add_<columna>_to_<tabla>_table` para añadir, `rename_x_to_y` para renombrar.

## Anatomía de una migración

```php
return new class extends Migration
{
    public function up(): void       // se ejecuta con: php artisan migrate
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void     // se ejecuta con: php artisan migrate:rollback
    {
        Schema::dropIfExists('categories');
    }
};
```

`up()` aplica el cambio y `down()` lo deshace. **Escribe siempre `down()`**: sin él la
migración no se puede revertir y `migrate:fresh` deja la base a medias.

## El orden de ejecución lo decide el nombre del archivo

Laravel ordena las migraciones **alfabéticamente por nombre de archivo**, no por el
orden en que las creaste. Como el nombre empieza por el timestamp, normalmente eso
equivale al orden cronológico.

El problema aparece cuando generas varias migraciones en el mismo segundo: comparten
timestamp y el desempate pasa a ser el texto siguiente. En este proyecto ocurrió justo
eso:

```
2026_07_28_210044_create_tag_task_table.php   ← se ejecutaría PRIMERO
2026_07_28_210044_create_tasks_table.php      ← se ejecutaría DESPUÉS
```

`tag_task` va antes que `tasks` porque en la cuarta letra `g` es menor que `s`. La pivot
intentaría crear su clave foránea contra una tabla que todavía no existe y la migración
fallaría.

**Solución:** renombrar los archivos para que el timestamp refleje la dependencia. Una
tabla debe crearse después de todas aquellas a las que apunta con claves foráneas.

```
210044_create_categories_table    (sin dependencias)
210045_create_tags_table          (sin dependencias)
210046_create_tasks_table         (depende de categories)
210047_create_tag_task_table      (depende de tags y tasks)
```

## Tipos de columna más usados

```php
$table->id();                          // bigint unsigned, PK, auto_increment
$table->string('title');               // varchar(255)
$table->string('name', 100);           // varchar(100)
$table->text('description');           // text, para textos largos
$table->boolean('is_completed');       // tinyint(1)
$table->integer('stock');
$table->decimal('price', 8, 2);
$table->date('due_date');
$table->timestamps();                  // created_at y updated_at
```

## Modificadores

Se encadenan al tipo de columna:

```php
$table->text('description')->nullable();          // permite NULL
$table->boolean('is_completed')->default(false);  // valor por defecto
$table->string('name', 100)->unique();            // índice único
$table->string('slug')->index();                  // índice normal
```

`nullable()` es el que más problemas causa cuando se olvida. Si la columna es
obligatoria en la base de datos pero el formulario permite dejarla vacía, el insert
revienta con un error de SQL. **La regla: la migración y las reglas de validación
tienen que decir lo mismo.**

## Claves foráneas

```php
$table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
```

Desglosado:

| Parte | Qué hace |
| ----- | -------- |
| `foreignId('category_id')` | Crea la columna `bigint unsigned` |
| `nullable()` | Permite tareas sin categoría |
| `constrained()` | Crea la FK; deduce que apunta a `categories.id` por el nombre `category_id` |
| `nullOnDelete()` | Al borrar la categoría, pone la columna a `NULL` |

Comportamientos al borrar el registro padre:

| Método | Efecto sobre los hijos |
| ------ | ---------------------- |
| `cascadeOnDelete()` | Se borran también |
| `nullOnDelete()`    | Su columna queda en `NULL` (requiere `nullable()`) |
| `restrictOnDelete()`| Impide borrar el padre si tiene hijos |

La elección no es estética: si borras una categoría, `cascadeOnDelete()` se llevaría
por delante todas sus tareas. Aquí se usa `nullOnDelete()` porque una tarea sin
categoría sigue siendo una tarea válida.

## Tablas pivote (relación muchos a muchos)

Una tarea tiene varias etiquetas y una etiqueta está en varias tareas. Esa relación
necesita una tabla intermedia.

```php
Schema::create('tag_task', function (Blueprint $table) {
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->foreignId('task_id')->constrained()->cascadeOnDelete();

    $table->primary(['tag_id', 'task_id']);
});
```

Tres decisiones a destacar:

**El nombre es `tag_task`, no `task_tag`.** Laravel espera los dos nombres de modelo en
singular, en orden alfabético, unidos por guion bajo. Aquí `tag` va antes que `task`
porque en la cuarta letra `g` es menor que `s`. Si la nombras al revés, `belongsToMany`
buscará una tabla que no existe y tendrás que declarar el nombre a mano en los dos
modelos:

```php
// solo necesario si NO sigues la convención
return $this->belongsToMany(Tag::class, 'task_tag');
```

Es más simple acertar desde el principio. Para salir de dudas, pregúntale al propio
framework antes de escribir la migración:

```bash
php artisan tinker --execute='echo (new App\Models\Task)->tags()->getTable();'
```

**No lleva `$table->id()`.** Una pivot no necesita clave primaria propia: lo que la
identifica es la pareja de claves foráneas.

**Lleva `primary(['task_id', 'tag_id'])`.** La clave primaria compuesta impide a nivel
de base de datos que se inserte dos veces la misma pareja. Sin ella, nada evita
duplicados como "tarea 5 etiquetada con la etiqueta 3" repetido.

**No lleva `timestamps()`** porque no interesa cuándo se asoció la etiqueta. Si hiciera
falta, se añaden y se declara `->withTimestamps()` en la relación del modelo.

## Ejecutar y comprobar

```bash
php artisan migrate               # aplica las pendientes
php artisan migrate:status        # muestra cuáles se aplicaron
php artisan migrate:rollback      # revierte el último lote
php artisan migrate:fresh         # borra todas las tablas y vuelve a migrar
php artisan migrate:fresh --seed  # igual, y además ejecuta los seeders
```

Antes de dar una migración por buena, comprueba que se puede **revertir**:

```bash
php artisan migrate:rollback --step=4
php artisan migrate
```

Si el rollback falla, el `down()` está mal. Es un error que no se nota hasta que
necesitas deshacer algo con prisa.

## Errores frecuentes

| Error | Causa |
| ----- | ----- |
| `Cannot add foreign key constraint` | La tabla referenciada aún no existe: revisa el orden de los timestamps |
| `Field 'description' doesn't have a default value` | La columna es `NOT NULL` pero se insertó sin valor: falta `nullable()` |
| `Duplicate column name` | Se creó dos veces la misma columna, normalmente por una migración duplicada |
| `could not find driver` | Falta la extensión PDO (`pdo_mysql`) o el contenedor no está levantado |

Una regla que ahorra disgustos: **si una migración ya se subió al repositorio y otros la
ejecutaron, no la edites.** Crea una migración nueva que corrija lo anterior. Editarla
deja tu base de datos y la de tus compañeros en estados distintos, sin que git lo
detecte.
