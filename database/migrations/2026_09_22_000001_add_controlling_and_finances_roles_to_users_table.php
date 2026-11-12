<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'worker', 'viewer', 'jefatura', 'controlling', 'finances') NOT NULL DEFAULT 'worker'");

        // Conserva la operación actual al convertir los perfiles departamentales
        // existentes, sin tocar administradores ni jefaturas.
        DB::table('users')
            ->where('role', 'worker')
            ->where('departamento', 'Controlling')
            ->update(['role' => 'controlling']);

        DB::table('users')
            ->where('role', 'worker')
            ->where('departamento', 'Finanzas')
            ->update(['role' => 'finances']);

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            $modules = json_decode($user->authorized_modules ?? '[]', true) ?: [];
            $modules = array_values(array_filter($modules, fn (string $module): bool => $module !== 'finances'));

            if (in_array($user->role, ['jefatura', 'controlling', 'finances'], true)
                && !in_array('all', $modules, true)
                && !in_array('renditions', $modules, true)) {
                $modules[] = 'renditions';
            }

            DB::table('users')->where('id', $user->id)->update([
                'authorized_modules' => json_encode(array_values(array_unique($modules))),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('role', ['controlling', 'finances'])
            ->update(['role' => 'worker']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'worker', 'viewer', 'jefatura') NOT NULL DEFAULT 'worker'");
    }
};
