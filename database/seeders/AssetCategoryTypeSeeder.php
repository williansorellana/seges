<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategoryTypeSeeder extends Seeder
{
    /**
     * Clasifica las categorías de activos en Hardware o Software.
     *
     * Este seeder permite dejar documentada y automatizada la separación
     * solicitada para el módulo de activos, evitando modificar los registros
     * manualmente desde phpMyAdmin.
     *
     * Uso:
     * php artisan db:seed --class=AssetCategoryTypeSeeder
     */
    public function run(): void
    {
        // Categorías físicas o tangibles.
        AssetCategory::whereIn('nombre', [
            'Equipos de Oficina',
            'Herramientas',
            'Mobiliario',
            'Equipos de Construcción',
            'Vehículos Menores',
            'Tecnología',
        ])->update([
            'tipo' => 'hardware',
        ]);

        // Categorías lógicas, digitales o asociadas a licencias.
        AssetCategory::whereIn('nombre', [
            'Software',
            'Licencias',
            'Microsoft Office',
            'Office 365',
            'Antivirus',
            'ERP',
            'Sistema',
            'Sistemas',
            'Aplicaciones',
        ])->update([
            'tipo' => 'software',
        ]);
    }
}