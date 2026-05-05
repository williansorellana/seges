<?php

return [

    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe superar los :max caracteres.',
    ],

    'attributes' => [
        'vehicle_id' => 'vehículo',
        'start_date' => 'fecha de inicio',
        'end_date' => 'fecha de término',
        'origin' => 'origen',
        'fuel_level' => 'nivel de combustible',
    ],

];