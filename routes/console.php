<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Muestra una cita inspiradora');

// Programar chequeo diario de documentos vencidos
use Illuminate\Support\Facades\Schedule;

Schedule::command('vehicles:check-documents')->dailyAt('09:00');
Schedule::command('vehicles:check-maintenance')->dailyAt('09:00');
Schedule::command('assets:check-alerts')->dailyAt('09:00');
Schedule::command('app:check-license-expirations')->dailyAt('08:00'); // Chequeo de licencias a las 8 AM
Schedule::command('reminders:send')->everyMinute();
