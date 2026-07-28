<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Depende de CategorySeeder y TagSeeder: las categorías y etiquetas se buscan
     * por nombre, así que deben existir antes de ejecutar este seeder.
     */
    public function run(): void
    {
        $tareas = [
            [
                'title' => 'Preparar informe trimestral',
                'description' => 'Reunir las métricas de los tres últimos meses y redactar el resumen.',
                'category' => 'Trabajo',
                'is_completed' => false,
                'tags' => ['urgente', 'importante'],
            ],
            [
                'title' => 'Revisar propuesta del proveedor',
                'description' => null,
                'category' => 'Trabajo',
                'is_completed' => true,
                'tags' => ['revisar'],
            ],
            [
                'title' => 'Renovar el pasaporte',
                'description' => 'Pedir cita previa y llevar la documentación.',
                'category' => 'Personal',
                'is_completed' => false,
                'tags' => ['pendiente'],
            ],
            [
                'title' => 'Terminar el módulo de Laravel',
                'description' => 'Repasar migraciones, modelos y relaciones.',
                'category' => 'Estudio',
                'is_completed' => false,
                'tags' => ['importante', 'pendiente'],
            ],
            [
                'title' => 'Comprar material de oficina',
                'description' => null,
                'category' => 'Hogar',
                'is_completed' => true,
                'tags' => [],
            ],
            [
                'title' => 'Planificar la reunión de equipo',
                'description' => 'Preparar el orden del día y reservar la sala.',
                'category' => null,
                'is_completed' => false,
                'tags' => ['reunión', 'urgente'],
            ],
        ];

        foreach ($tareas as $datos) {
            // Se busca por título para que el seeder sea idempotente. No se usa
            // firstOrCreate() ni updateOrCreate() porque implicarían asignación masiva.
            $task = Task::firstWhere('title', $datos['title']) ?? new Task;
            $task->title = $datos['title'];
            $task->description = $datos['description'];
            $task->category_id = $datos['category']
                ? Category::where('name', $datos['category'])->value('id')
                : null;
            $task->is_completed = $datos['is_completed'];
            $task->save();

            $task->tags()->sync(
                Tag::whereIn('name', $datos['tags'])->pluck('id')->all()
            );
        }
    }
}
