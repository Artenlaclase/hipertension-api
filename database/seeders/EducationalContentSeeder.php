<?php

namespace Database\Seeders;

use App\Models\EducationalContent;
use Illuminate\Database\Seeder;

class EducationalContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            [
                'title'   => '¿Qué es la hipertensión?',
                'content' => 'La hipertensión arterial es una condición en la que la fuerza de la sangre contra las paredes de las arterias es consistentemente alta. Se diagnostica cuando la presión sistólica es ≥140 mmHg o la diastólica ≥90 mmHg.',
                'topic'   => 'hipertensión',
                'level'   => 'básico',
            ],
            [
                'title'   => 'Dieta DASH para hipertensos',
                'content' => 'La dieta DASH (Dietary Approaches to Stop Hypertension) enfatiza frutas, verduras, granos integrales, proteínas magras y lácteos bajos en grasa. Reduce el sodio y aumenta el potasio, magnesio y calcio.',
                'topic'   => 'nutrición',
                'level'   => 'intermedio',
            ],
            [
                'title'   => 'Sodio y presión arterial',
                'content' => 'El exceso de sodio retiene líquidos, aumentando el volumen sanguíneo y la presión. Se recomienda no superar 2,300 mg/día. Para hipertensos, el ideal es menos de 1,500 mg/día.',
                'topic'   => 'nutrición',
                'level'   => 'básico',
            ],
            [
                'title'   => 'Potasio: el aliado contra la hipertensión',
                'content' => 'El potasio ayuda a equilibrar los efectos del sodio. Fuentes ricas: banano, espinaca, aguacate, salmón y frijoles. La ingesta recomendada es 3,500–5,000 mg/día.',
                'topic'   => 'nutrición',
                'level'   => 'intermedio',
            ],
            [
                'title'   => 'Ejercicio y presión arterial',
                'content' => 'La actividad física regular (150 min/semana de ejercicio moderado) puede reducir la PA sistólica entre 5-8 mmHg. Se recomiendan caminatas, natación y ciclismo.',
                'topic'   => 'hábitos',
                'level'   => 'básico',
            ],
            [
                'title'   => 'Estrés e hipertensión',
                'content' => 'El estrés crónico contribuye a la hipertensión. Técnicas como meditación, respiración profunda y yoga han demostrado reducir la PA de forma significativa.',
                'topic'   => 'hábitos',
                'level'   => 'básico',
            ],
            [
                'title'   => 'Cómo medir correctamente la presión arterial',
                'content' => 'Siéntese tranquilo 5 minutos antes. Use un brazalete adecuado. Coloque el brazo a la altura del corazón. No hable durante la medición. Tome 2-3 lecturas con 1 minuto de diferencia.',
                'topic'   => 'hipertensión',
                'level'   => 'básico',
            ],
            [
                'title'   => 'Medicamentos antihipertensivos comunes',
                'content' => 'Los principales grupos incluyen: IECA, ARA-II, betabloqueadores, diuréticos y calcioantagonistas. Nunca suspenda su medicación sin consultar a su médico.',
                'topic'   => 'medicamentos',
                'level'   => 'intermedio',
            ],
        ];

        foreach ($contents as $content) {
            EducationalContent::create($content);
        }
    }
}
