<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class TenVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'plate' => 'KJKL-98',
                'year' => 2023,
                'mileage' => 15000,
                'status' => 'available',
                'serial_number' => 'TOY-HI-001',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Chevrolet',
                'model' => 'D-Max',
                'plate' => 'LXRT-22',
                'year' => 2022,
                'mileage' => 45000,
                'status' => 'available',
                'serial_number' => 'CHE-DM-002',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Nissan',
                'model' => 'NP300',
                'plate' => 'PRST-45',
                'year' => 2024,
                'mileage' => 5000,
                'status' => 'available',
                'serial_number' => 'NIS-NP-003',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Ford',
                'model' => 'Ranger',
                'plate' => 'ABCD-12',
                'year' => 2021,
                'mileage' => 60000,
                'status' => 'available',
                'serial_number' => 'FOR-RA-004',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Mitsubishi',
                'model' => 'L200',
                'plate' => 'XYZW-89',
                'year' => 2023,
                'mileage' => 22000,
                'status' => 'available',
                'serial_number' => 'MIT-L2-005',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Volkswagen',
                'model' => 'Amarok',
                'plate' => 'VWZX-34',
                'year' => 2022,
                'mileage' => 35000,
                'status' => 'available',
                'serial_number' => 'VW-AM-006',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Peugeot',
                'model' => 'Partner',
                'plate' => 'PGTP-56',
                'year' => 2024,
                'mileage' => 2000,
                'status' => 'available',
                'serial_number' => 'PEU-PA-007',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Hyundai',
                'model' => 'H-1',
                'plate' => 'HYUN-78',
                'year' => 2019,
                'mileage' => 90000,
                'status' => 'available',
                'serial_number' => 'HYU-H1-008',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'Maxus',
                'model' => 'T60',
                'plate' => 'MAXU-90',
                'year' => 2023,
                'mileage' => 12000,
                'status' => 'available',
                'serial_number' => 'MAX-T6-009',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
            [
                'brand' => 'JAC',
                'model' => 'T8',
                'plate' => 'JACT-11',
                'year' => 2023,
                'mileage' => 18000,
                'status' => 'available',
                'serial_number' => 'JAC-T8-010',
                'fuel_type' => 'diesel',
                'image_path' => 'vehicles/VMOKwY3UO3TgP6YOOhiSI0aoMs7x9qcvahzFOxNO.jpg'
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
