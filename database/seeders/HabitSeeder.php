<?php

namespace Database\Seeders;

use App\Models\Habit;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    public function run(): void
    {
        $habits = [
            ['name' => 'Beber 8 vasos de agua',         'description' => 'Hidratación adecuada ayuda a regular la presión arterial.'],
            ['name' => 'Caminar 30 minutos',             'description' => 'Actividad cardiovascular moderada diaria.'],
            ['name' => 'Reducir consumo de sal',          'description' => 'Limitar sodio a menos de 2,300 mg/día.'],
            ['name' => 'Meditar o relajarse 10 minutos',  'description' => 'Control del estrés para reducir la PA.'],
            ['name' => 'Dormir 7-8 horas',               'description' => 'Sueño reparador contribuye a la salud cardiovascular.'],
            ['name' => 'Consumir frutas y verduras',      'description' => 'Al menos 5 porciones diarias ricas en potasio.'],
            ['name' => 'Evitar alcohol',                  'description' => 'El alcohol eleva la presión arterial.'],
            ['name' => 'No fumar',                        'description' => 'El tabaco daña los vasos sanguíneos.'],
            ['name' => 'Tomar medicamentos a tiempo',     'description' => 'Adherencia al tratamiento prescrito.'],
            ['name' => 'Monitorear presión arterial',     'description' => 'Registro diario de PA sistólica y diastólica.'],
        ];

        foreach ($habits as $habit) {
            Habit::create($habit);
        }
    }
}
