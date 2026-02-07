<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // Frutas
            ['name' => 'Banano',     'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'frutas'],
            ['name' => 'Naranja',    'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'frutas'],
            ['name' => 'Manzana',    'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'frutas'],
            ['name' => 'Sandía',     'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'frutas'],
            ['name' => 'Aguacate',   'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'frutas'],

            // Verduras
            ['name' => 'Espinaca',   'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'verduras'],
            ['name' => 'Brócoli',    'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'verduras'],
            ['name' => 'Zanahoria',  'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'verduras'],
            ['name' => 'Tomate',     'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'verduras'],
            ['name' => 'Pepino',     'sodium_level' => 'bajo', 'potassium_level' => 'bajo', 'category' => 'verduras'],

            // Proteínas
            ['name' => 'Pechuga de pollo', 'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'proteínas'],
            ['name' => 'Salmón',           'sodium_level' => 'bajo', 'potassium_level' => 'alto', 'category' => 'proteínas'],
            ['name' => 'Huevo',            'sodium_level' => 'bajo', 'potassium_level' => 'bajo', 'category' => 'proteínas'],
            ['name' => 'Atún enlatado',    'sodium_level' => 'alto', 'potassium_level' => 'medio', 'category' => 'proteínas'],

            // Lácteos
            ['name' => 'Yogur natural',    'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'lácteos'],
            ['name' => 'Queso fresco',     'sodium_level' => 'medio', 'potassium_level' => 'bajo', 'category' => 'lácteos'],
            ['name' => 'Leche descremada', 'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'lácteos'],

            // Cereales
            ['name' => 'Avena',        'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'cereales'],
            ['name' => 'Arroz integral', 'sodium_level' => 'bajo', 'potassium_level' => 'medio', 'category' => 'cereales'],
            ['name' => 'Pan integral',  'sodium_level' => 'medio', 'potassium_level' => 'bajo', 'category' => 'cereales'],

            // Evitar (alto sodio)
            ['name' => 'Embutidos',       'sodium_level' => 'alto', 'potassium_level' => 'bajo', 'category' => 'procesados'],
            ['name' => 'Papas fritas',    'sodium_level' => 'alto', 'potassium_level' => 'medio', 'category' => 'procesados'],
            ['name' => 'Sopas instantáneas', 'sodium_level' => 'alto', 'potassium_level' => 'bajo', 'category' => 'procesados'],
        ];

        foreach ($foods as $food) {
            Food::create($food);
        }
    }
}
