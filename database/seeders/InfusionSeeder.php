<?php

namespace Database\Seeders;

use App\Models\Infusion;
use Illuminate\Database\Seeder;

class InfusionSeeder extends Seeder
{
    public function run(): void
    {
        $infusions = [
            // ── SEGURAS (beneficiosas para HTA) ─────────────────────
            [
                'name'             => 'Té de hibisco (Jamaica)',
                'description'      => 'Infusión de flores de hibisco, rica en antioxidantes y antocianinas.',
                'benefits'         => 'Estudios muestran reducción de PA sistólica entre 7-14 mmHg con consumo regular. Efecto diurético natural.',
                'preparation'      => 'Hervir 1-2 cucharadas de flor seca en 250ml de agua por 5-10 minutos. Se puede tomar frío o caliente.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],
            [
                'name'             => 'Té de manzanilla',
                'description'      => 'Infusión relajante de flores de manzanilla (Matricaria chamomilla).',
                'benefits'         => 'Reduce el estrés y la ansiedad, factores que elevan la PA. Propiedades antiinflamatorias.',
                'preparation'      => 'Infusionar 1 bolsita o 1 cucharada de flores en 250ml de agua caliente por 5 minutos.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 4,
            ],
            [
                'name'             => 'Té de valeriana',
                'description'      => 'Infusión de raíz de valeriana, usada tradicionalmente como sedante natural.',
                'benefits'         => 'Favorece la relajación y el sueño. El descanso adecuado contribuye al control de la PA.',
                'preparation'      => 'Hervir 1 cucharadita de raíz seca en 250ml de agua por 10 minutos. Tomar antes de dormir.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 2,
            ],
            [
                'name'             => 'Té de espino blanco (Hawthorn)',
                'description'      => 'Infusión de bayas y hojas de Crataegus, usada en fitoterapia cardiovascular.',
                'benefits'         => 'Mejora la circulación, reduce la resistencia vascular periférica. Uso tradicional para salud cardíaca.',
                'preparation'      => 'Infusionar 1-2 cucharaditas de bayas/hojas secas en 250ml de agua caliente por 10-15 minutos.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],
            [
                'name'             => 'Té de olivo (hojas)',
                'description'      => 'Infusión de hojas de olivo, rica en oleuropeína.',
                'benefits'         => 'La oleuropeína tiene efecto antihipertensivo demostrado. Ayuda a reducir PA sistólica y diastólica.',
                'preparation'      => 'Hervir 5-6 hojas de olivo en 250ml de agua por 10 minutos. Colar y tomar.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],
            [
                'name'             => 'Té de lavanda',
                'description'      => 'Infusión de flores de lavanda con efecto calmante.',
                'benefits'         => 'Reduce estrés y ansiedad. Contribuye indirectamente al control de PA por relajación.',
                'preparation'      => 'Infusionar 1-2 cucharaditas de flores secas en 250ml de agua caliente por 5 minutos.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],
            [
                'name'             => 'Té de pasiflora (Passiflora)',
                'description'      => 'Infusión de hojas y flores de pasiflora, sedante suave natural.',
                'benefits'         => 'Calma el sistema nervioso, reduce la ansiedad. Puede ayudar a disminuir PA asociada al estrés.',
                'preparation'      => 'Infusionar 1 cucharadita de hojas/flores secas en 250ml de agua caliente por 8 minutos.',
                'precaution_level' => 'safe',
                'precaution_note'  => null,
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],

            // ── PRECAUCIÓN (consumir con moderación) ────────────────
            [
                'name'             => 'Té verde',
                'description'      => 'Infusión de hojas de Camellia sinensis sin oxidar. Contiene cafeína y L-teanina.',
                'benefits'         => 'Antioxidante potente (catequinas). Estudios sugieren reducción moderada de PA a largo plazo.',
                'preparation'      => 'Infusionar en agua a 70-80°C por 2-3 minutos. No usar agua hirviendo.',
                'precaution_level' => 'caution',
                'precaution_note'  => 'Contiene cafeína (25-50mg/taza). Puede interactuar con medicamentos antihipertensivos y anticoagulantes. Limitar a 2-3 tazas/día.',
                'category'         => 'tea',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 3,
            ],
            [
                'name'             => 'Té de jengibre',
                'description'      => 'Infusión de raíz de jengibre fresco o seco.',
                'benefits'         => 'Mejora la circulación, antiinflamatorio. Algunos estudios indican efecto hipotensor leve.',
                'preparation'      => 'Rallar 1-2cm de raíz fresca en 250ml de agua caliente. Hervir 5 minutos.',
                'precaution_level' => 'caution',
                'precaution_note'  => 'Puede interactuar con anticoagulantes (warfarina) y bloqueadores de canales de calcio. Consultar al médico si toma medicamentos para HTA.',
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 2,
            ],
            [
                'name'             => 'Té negro',
                'description'      => 'Infusión de hojas de Camellia sinensis completamente oxidadas.',
                'benefits'         => 'Flavonoides que pueden mejorar la función endotelial.',
                'preparation'      => 'Infusionar 1 bolsita en 250ml de agua a 95°C por 3-5 minutos.',
                'precaution_level' => 'caution',
                'precaution_note'  => 'Mayor contenido de cafeína que el té verde (40-70mg/taza). La cafeína puede elevar temporalmente la PA. Máximo 2 tazas/día.',
                'category'         => 'tea',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 2,
            ],
            [
                'name'             => 'Té de canela',
                'description'      => 'Infusión de corteza de canela (Cinnamomum verum).',
                'benefits'         => 'Puede ayudar a reducir la PA y la glucemia. Propiedades antiinflamatorias.',
                'preparation'      => 'Hervir 1 rama de canela en 300ml de agua por 10 minutos.',
                'precaution_level' => 'caution',
                'precaution_note'  => 'La canela cassia (la más común) contiene cumarina, hepatotóxica en exceso. Puede interactuar con antidiabéticos. Preferir canela de Ceilán.',
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 2,
            ],
            [
                'name'             => 'Té de romero',
                'description'      => 'Infusión de hojas de Rosmarinus officinalis.',
                'benefits'         => 'Antioxidante, mejora la circulación.',
                'preparation'      => 'Infusionar 1 cucharadita de hojas secas en 250ml de agua caliente por 5-8 minutos.',
                'precaution_level' => 'caution',
                'precaution_note'  => 'En dosis altas puede ELEVAR la presión arterial. Limitar a 1-2 tazas/día. Evitar si la PA está descontrolada.',
                'category'         => 'herbal',
                'recommended_ml'   => 250,
                'max_daily_cups'   => 2,
            ],

            // ── EVITAR (puede elevar PA o interacciones peligrosas) ─
            [
                'name'             => 'Té de regaliz (Licorice)',
                'description'      => 'Infusión de raíz de Glycyrrhiza glabra.',
                'benefits'         => 'Propiedades antiinflamatorias generales, pero contraindicado en HTA.',
                'preparation'      => 'No recomendado para personas con hipertensión.',
                'precaution_level' => 'avoid',
                'precaution_note'  => '🔴 CONTRAINDICADO en hipertensión. La glicirricina eleva la PA al inhibir la enzima 11β-HSD2, causando retención de sodio y pérdida de potasio. Puede causar hipopotasemia.',
                'category'         => 'herbal',
                'recommended_ml'   => 0,
                'max_daily_cups'   => 0,
            ],
            [
                'name'             => 'Té de ginseng',
                'description'      => 'Infusión de raíz de Panax ginseng.',
                'benefits'         => 'Adaptógeno y estimulante general.',
                'preparation'      => 'No recomendado para personas con hipertensión sin supervisión médica.',
                'precaution_level' => 'avoid',
                'precaution_note'  => '🔴 Puede elevar significativamente la PA. Interactúa con medicamentos antihipertensivos, anticoagulantes y antidiabéticos. No consumir sin autorización médica.',
                'category'         => 'herbal',
                'recommended_ml'   => 0,
                'max_daily_cups'   => 0,
            ],
            [
                'name'             => 'Café / infusiones con alto cafeína',
                'description'      => 'Bebidas con alta concentración de cafeína (>100mg por porción).',
                'benefits'         => 'Estimulante del sistema nervioso central.',
                'preparation'      => 'N/A',
                'precaution_level' => 'avoid',
                'precaution_note'  => '🔴 La cafeína en exceso eleva la PA de forma aguda (5-10 mmHg). Personas sensibles o con HTA no controlada deben evitar más de 200mg/día de cafeína total.',
                'category'         => 'other',
                'recommended_ml'   => 0,
                'max_daily_cups'   => 0,
            ],
            [
                'name'             => 'Té de efedra (Ma Huang)',
                'description'      => 'Infusión de Ephedra sinica, estimulante potente.',
                'benefits'         => 'Uso tradicional como descongestionante.',
                'preparation'      => 'No consumir bajo ninguna circunstancia con hipertensión.',
                'precaution_level' => 'avoid',
                'precaution_note'  => '🔴 PELIGROSO. La efedrina es un simpaticomimético que eleva drásticamente la PA y la frecuencia cardíaca. Prohibida en muchos países. Riesgo de evento cardiovascular.',
                'category'         => 'herbal',
                'recommended_ml'   => 0,
                'max_daily_cups'   => 0,
            ],
        ];

        foreach ($infusions as $infusion) {
            Infusion::updateOrCreate(
                ['name' => $infusion['name']],
                $infusion
            );
        }
    }
}
