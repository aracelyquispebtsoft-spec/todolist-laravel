<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nombres = ['urgente', 'importante', 'revisar', 'pendiente', 'reunión'];

        foreach ($nombres as $nombre) {
            // Igual que en CategorySeeder: se busca antes de crear para poder
            // relanzar el seeder sin violar el índice único de name.
            $tag = Tag::firstWhere('name', $nombre) ?? new Tag;
            $tag->name = $nombre;
            $tag->save();
        }
    }
}
