# To-Do List

Aplicación web para la gestión de tareas: permite registrar, listar, editar y eliminar
tareas, organizándolas mediante categorías y etiquetas.

## Requerimientos

### Funcionales

**Gestión de tareas**

- Crear una tarea con título, descripción, una categoría, una o varias etiquetas y
  estado (realizada o no)
- Ver, editar y eliminar una tarea
- Listar todas las tareas

**Gestión de categorías**

- Crear una categoría con nombre
- Ver, editar y eliminar una categoría
- Listar las categorías disponibles

**Gestión de etiquetas**

- Crear una etiqueta con nombre
- Ver, editar y eliminar una etiqueta
- Listar las etiquetas disponibles

**Interfaz web**

- Formularios con validación
- Vistas construidas con Blade
- Navegación entre secciones

### No funcionales

- **Sin autenticación**: no se maneja login ni sesiones de usuario (se implementará en
  el siguiente proyecto)
- **Responsive**: navegable desde dispositivos móviles y escritorio
- **Persistencia**: los datos se almacenan en MySQL

## Stack

| Componente | Versión                 |
| ---------- | ----------------------- |
| PHP        | 8.3                     |
| Laravel    | 12.64                   |
| MySQL      | 8.4 (contenedor Docker) |
| Blade      | motor de plantillas     |
| Vite       | build de assets         |

## Puesta en marcha

Requiere PHP 8.3+ con la extensión `pdo_mysql`, Composer 2, Node 20+ y Docker.

```bash
# 1. Dependencias
composer install
npm install

# 2. Variables de entorno
cp .env.example .env
php artisan key:generate

# 3. Base de datos
docker compose up -d          # levanta MySQL 8.4
docker compose ps             # esperar a que aparezca (healthy)
php artisan migrate

# 4. Servidor de desarrollo
php artisan serve             # http://127.0.0.1:8000
npm run dev                   # en otra terminal, para los assets
```

## Base de datos

El servicio MySQL se define en `docker-compose.yml` y toma sus credenciales y puerto
del archivo `.env`, de modo que ambos se mantienen sincronizados.

```bash
docker compose up -d          # levantar
docker compose down           # detener (los datos persisten)
docker compose down -v        # detener y borrar los datos
```

Los datos viven en el volumen `mysql-data` y sobreviven al reinicio del contenedor.

## Comandos útiles

```bash
php artisan migrate:fresh --seed   # recrear el esquema con datos de prueba
php artisan test                   # ejecutar la suite de pruebas
php artisan route:list             # listar las rutas registradas
```

## Convenciones

Los mensajes de commit siguen [Conventional Commits](https://www.conventionalcommits.org/):

```
<tipo>(<ámbito>): <descripción en imperativo>
```

Tipos empleados: `feat`, `fix`, `docs`, `test`, `refactor`, `chore`.

Cada funcionalidad se desarrolla en su propia rama (`feat/...`) y se integra en `main`
mediante `git merge --no-ff`, de forma que el historial conserve el agrupamiento por
funcionalidad.
