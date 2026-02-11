<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\LicenseExpirationAlert;
use Carbon\Carbon;

class CheckLicenseExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-license-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chequea diariamente si hay licencias por vencer (30, 15, 7 días) o vencidas y envía notificaciones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando chequeo de vencimiento de licencias...');

        $today = Carbon::today();

        // Usuarios con licencia (license_expires_at no nulo)
        $users = User::whereNotNull('license_expires_at')->get();

        foreach ($users as $user) {
            $expirationDate = Carbon::parse($user->license_expires_at)->startOfDay();
            $daysRemaining = (int) $today->diffInDays($expirationDate, false);

            // Alertas solicitadas: 30 días, 15 días, 7 días
            // Además: Si ya venció (podríamos avisar el día exacto de vencimiento, o sea 0 o -1)

            // Lógica:
            // Si faltan exactamente 30, 15 o 7 días -> Aviso pre-vencimiento
            // Si faltan 0 días (vence hoy) o -1 (venció ayer) -> Aviso vencimiento.

            // Vamos a alertar en días exactos para evitar spam diario.
            // 30 días, 15 días, 7 días, 0 días (hoy vence), -1 día (ya venció ayer y se bloquea).

            if (in_array($daysRemaining, [30, 15, 7])) {
                $user->notify(new LicenseExpirationAlert($daysRemaining));
                $this->info("Notificación enviada a {$user->name} (Faltan {$daysRemaining} días).");
            } elseif ($daysRemaining === 0) {
                // Vence HOY
                $user->notify(new LicenseExpirationAlert(0));
                $this->info("Notificación enviada a {$user->name} (Vence HOY).");
            } elseif ($daysRemaining === -1) {
                // Venció AYER
                $user->notify(new LicenseExpirationAlert(-1));
                $this->info("Notificación enviada a {$user->name} (Venció ayer).");
            }
        }

        $this->info('Chequeo finalizado.');
    }
}
