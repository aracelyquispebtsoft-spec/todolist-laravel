<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Número de categorías a generar. No puede superar el tamaño del vocabulario
     * de CategoryFactory: unique() se quedaría sin valores disponibles.
     */
    private const AMOUNT = 5;

    /**
     * Run the database seeds.
     *
     * Los datos los genera CategoryFactory. Se leen con definition() y se asignan
     * atributo por atributo, porque factory()->create() desactiva internamente la
     * guarda del modelo y haría asignación masiva.
     *
     * El vocabulario del factory es finito, así que al relanzar el seeder puede
     * repetirse un nombre ya existente; se comprueba antes de guardar para no
     * violar el índice único.
     */
    public function run(): void
    {
        $factory = Category::factory();

        for ($i = 0; $i < self::AMOUNT; $i++) {
            $datos = $factory->definition();

            if (Category::where('name', $datos['name'])->exists()) {
                continue;
            }

            $category = new Category;
            $category->name = $datos['name'];
            $category->save();
        }
    }
}
