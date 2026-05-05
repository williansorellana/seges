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
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'after' => 'El campo :attribute debe ser posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'exists' => 'El campo seleccionado en :attribute no es válido.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'array' => 'El campo :attribute debe ser una lista válida.',
    'image' => 'El campo :attribute debe ser una imagen válida.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',

    'attributes' => [
        'vehicle_id' => 'vehículo',
        'start_date' => 'fecha de inicio',
        'end_date' => 'fecha de término',
        'origin' => 'origen',
        'fuel_level' => 'nivel de combustible',
        'destination_type' => 'tipo de viaje',
        'destination' => 'destino',
        'return_mileage' => 'kilometraje de devolución',
        'tire_status_front' => 'estado de neumáticos delanteros',
        'tire_status_rear' => 'estado de neumáticos traseros',
        'cleanliness' => 'limpieza',
        'photos' => 'fotos',
        'photos.*' => 'foto',
        'comments' => 'comentarios',
    ],

];