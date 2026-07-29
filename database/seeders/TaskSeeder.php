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
     * Depende de CategorySeeder y TagSeeder: las categorías se toman al azar entre
     * las existentes y las etiquetas se buscan por nombre, así que ambas deben
     * haberse generado antes.
     *
     * Se busca cada tarea por título para que el seeder pueda relanzarse. No se usa
     * firstOrCreate() ni updateOrCreate() porque implicarían asignación masiva.
     */
    public function run(): void
    {
        $tareas = [
            [
                'title' => 'Preparar informe trimestral',
                'description' => 'Reunir las métricas de los tres últimos meses y redactar el resumen.',
                'has_category' => true,
                'is_completed' => false,
                'tags' => ['urgente', 'importante'],
            ],
            [
                'title' => 'Revisar propuesta del proveedor',
                'description' => null,
                'has_category' => true,
                'is_completed' => true,
                'tags' => ['revisar'],
            ],
            [
                'title' => 'Renovar el pasaporte',
                'description' => 'Pedir cita previa y llevar la documentación.',
                'has_category' => true,
                'is_completed' => false,
                'tags' => ['pendiente'],
            ],
            [
                'title' => 'Terminar el módulo de Laravel',
                'description' => 'Repasar migraciones, modelos y relaciones.',
                'has_category' => true,
                'is_completed' => false,
                'tags' => ['importante', 'pendiente'],
            ],
            [
                'title' => 'Comprar material de oficina',
                'description' => null,
                'has_category' => true,
                'is_completed' => true,
                'tags' => [],
            ],
            [
                'title' => 'Planificar la reunión de equipo',
                'description' => 'Preparar el orden del día y reservar la sala.',
                'has_category' => false,
                'is_completed' => false,
                'tags' => ['reunión', 'urgente'],
            ],
        ];

        foreach ($tareas as $datos) {
            $task = Task::firstWhere('title', $datos['title']) ?? new Task;
            $task->title = $datos['title'];
            $task->description = $datos['description'];
            $task->category_id = $datos['has_category']
                ? Category::inRandomOrder()->value('id')
                : null;
            $task->is_completed = $datos['is_completed'];
            $task->save();

            $task->tags()->sync(
                Tag::whereIn('name', $datos['tags'])->pluck('id')->all()
            );
        }
    }
}
