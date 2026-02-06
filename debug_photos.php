<?php

use App\Models\VehicleReturn;

$latestReturn = VehicleReturn::latest()->first();

if ($latestReturn) {
    echo "ID: " . $latestReturn->id . "\n";
    echo "Photos Paths (Raw): " . json_encode($latestReturn->getAttributes()['photos_paths']) . "\n";
    echo "Photos Paths (Casted): " . json_encode($latestReturn->photos_paths) . "\n";
    echo "Is Array? " . (is_array($latestReturn->photos_paths) ? 'Yes' : 'No') . "\n";
    if (is_array($latestReturn->photos_paths)) {
        echo "Count: " . count($latestReturn->photos_paths) . "\n";
    }
} else {
    echo "No returns found.\n";
}
