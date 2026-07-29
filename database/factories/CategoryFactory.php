<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Faker no tiene un generador de categorías de tareas y sus palabras son
     * lorem ipsum («aspernatur», «odit»), así que el vocabulario se define aquí
     * y se elige al azar. unique() evita repeticiones, que romperían el índice
     * único de la columna name.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Trabajo',
                'Personal',
                'Estudio',
                'Hogar',
                'Finanzas',
                'Salud',
                'Compras',
                'Viajes',
                'Proyectos',
                'Ocio',
            ]),
        ];
    }
}
