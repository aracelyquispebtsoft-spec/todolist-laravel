<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nombres = ['Trabajo', 'Personal', 'Estudio', 'Hogar'];

        foreach ($nombres as $nombre) {
            // Se busca antes de crear para que el seeder se pueda relanzar sin
            // violar el índice único de name. No se usa firstOrCreate() porque
            // implicaría asignación masiva.
            $category = Category::firstWhere('name', $nombre) ?? new Category;
            $category->name = $nombre;
            $category->save();
        }
    }
}
