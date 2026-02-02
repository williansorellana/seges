<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\VehicleRequestController;
use App\Http\Controllers\ForceChangePasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleReturnController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\RoomReservationController;

// Página de inicio
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard 
Route::get('/dashboard', [VehicleController::class, 'index'])
    ->middleware(['auth', 'force.password.change'])
    ->name('dashboard');

// Grupo de rutas para el perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Vehículos (Papelera)
    Route::get('papelera/vehiculos', [VehicleController::class, 'trash'])->name('vehicles.trash');
    Route::put('papelera/vehiculos/{id}/restore', [VehicleController::class, 'restore'])->name('vehicles.restore');
    Route::delete('papelera/vehiculos/{id}/force-delete', [VehicleController::class, 'forceDelete'])->name('vehicles.force-delete');

    // Recurso de Vehículos
    Route::resource('vehiculos', VehicleController::class)
        ->names([
            'index' => 'vehicles.index',
            'create' => 'vehicles.create',
            'store' => 'vehicles.store',
            'show' => 'vehicles.show',
            'edit' => 'vehicles.edit',
            'update' => 'vehicles.update',
            'destroy' => 'vehicles.destroy',
        ])
        ->parameters(['vehiculos' => 'vehicle'])
        ->except(['show']);

    // Rutas de Conductores
    Route::get('/conductores/trash', [ConductorController::class, 'trash'])->name('conductores.trash');
    Route::post('/conductores/{id}/restore', [ConductorController::class, 'restore'])->name('conductores.restore');
    Route::delete('/conductores/{id}/force-delete', [ConductorController::class, 'forceDelete'])->name('conductores.force-delete');

    Route::get('/conductores', [ConductorController::class, 'index'])->name('conductores.index');
    Route::get('/conductores/nuevo', [ConductorController::class, 'create'])->name('conductores.create');
    Route::post('/conductores', [ConductorController::class, 'store'])->name('conductores.store');
    Route::get('/conductores/{conductor}/edit', [ConductorController::class, 'edit'])->name('conductores.edit');
    Route::put('/conductores/{conductor}', [ConductorController::class, 'update'])->name('conductores.update');
    Route::delete('/conductores/{conductor}', [ConductorController::class, 'destroy'])->name('conductores.destroy');

    // Gestión de Salas 
    Route::get('/reservar-sala', [RoomReservationController::class, 'index'])->name('reservations.catalog');
    Route::post('/reservar-sala', [RoomReservationController::class, 'store'])->name('reservations.store');
    Route::get('/mis-reservas-salas', [RoomReservationController::class, 'myReservations'])->name('reservations.my_reservations');
    Route::put('/mis-reservas-salas/{id}/cancel', [RoomReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/rooms/{room}/availability', [RoomReservationController::class, 'availability'])->name('rooms.availability');
    //filtrado solo admin y supervisor
    Route::middleware(['role:admin,supervisor'])->group(function () {

        Route::get('rooms/trash', [MeetingRoomController::class, 'trash'])->name('rooms.trash');
        Route::put('rooms/{id}/restore', [MeetingRoomController::class, 'restore'])->name('rooms.restore');
        Route::delete('rooms/{id}/force-delete', [MeetingRoomController::class, 'forceDelete'])->name('rooms.force-delete');
        Route::resource('rooms', MeetingRoomController::class);

        Route::put('/room-reservations/{id}/approve', [RoomReservationController::class, 'approve'])->name('room-reservations.approve');
        Route::put('/room-reservations/{id}/reject', [RoomReservationController::class, 'reject'])->name('room-reservations.reject');
        Route::put('/room-reservations/{id}/cancel-admin', [RoomReservationController::class, 'cancelByAdmin'])->name('room-reservations.cancel_admin');

        Route::get('/admin/rooms/agenda', [RoomReservationController::class, 'agenda'])->name('rooms.agenda');

        Route::get('/admin/rooms/history', [RoomReservationController::class, 'history'])->name('rooms.history');
        Route::get('/admin/rooms/report', [RoomReservationController::class, 'downloadMonthlyReport'])->name('rooms.report');
    });

    // Rutas de Mantenimiento
    Route::post('vehiculos/{vehicle}/maintenance/state', [MaintenanceController::class, 'updateState'])->name('vehicles.maintenance.state');
    Route::post('vehiculos/{vehicle}/maintenance/request', [MaintenanceController::class, 'storeRequest'])->name('vehicles.maintenance.request');
    Route::post('vehiculos/{vehicle}/maintenance/complete', [MaintenanceController::class, 'complete'])->name('vehicles.maintenance.complete');
    Route::post('maintenance/requests/{id}/accept', [MaintenanceController::class, 'acceptRequest'])->name('maintenance.requests.accept');

    // Trigger manual de alertas (Solo para pruebas/demo)
    // Trigger manual de alertas (Solo para pruebas/demo)
    Route::get('/maintenance/check', function (\App\Services\MaintenanceService $service) {
        $service->checkAndNotify();
        return redirect()->back()->with('success', 'Chequeo de alertas ejecutado.');
    })->name('maintenance.check');

    // Rutas de Solicitudes de Vehículos (Reservas)
    Route::get('/solicitar-vehiculo', [VehicleRequestController::class, 'create'])->name('requests.create');
    Route::post('/solicitar-vehiculo', [VehicleRequestController::class, 'store'])->name('requests.store');
    Route::post('/requests/{id}/approve', [VehicleRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [VehicleRequestController::class, 'reject'])->name('requests.reject');
    Route::get('/mis-reservas', [VehicleRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{id}/complete', [VehicleRequestController::class, 'complete'])->name('requests.complete');
    Route::get('/historial-uso-vehiculos', [VehicleRequestController::class, 'history'])->name('requests.history.index');
    Route::get('/historial-uso-vehiculos/papelera', [VehicleRequestController::class, 'trash'])->name('requests.history.trash');
    Route::delete('/historial-uso-vehiculos/{id}', [VehicleRequestController::class, 'destroy'])->name('requests.history.destroy');
    Route::put('/historial-uso-vehiculos/{id}/restore', [VehicleRequestController::class, 'restore'])->name('requests.history.restore');
    Route::delete('/historial-uso-vehiculos/{id}/force', [VehicleRequestController::class, 'forceDelete'])->name('requests.history.force-delete');


    // Historial de Devoluciones (Admin)
    Route::get('/admin/returns/trash', [VehicleReturnController::class, 'trash'])->name('admin.returns.trash');
    Route::put('/admin/returns/{id}/restore', [VehicleReturnController::class, 'restore'])->name('admin.returns.restore');
    Route::delete('/admin/returns/{id}/force-delete', [VehicleReturnController::class, 'forceDelete'])->name('admin.returns.force-delete');
    Route::delete('/admin/returns/{id}', [VehicleReturnController::class, 'destroy'])->name('admin.returns.destroy');
    Route::get('/admin/returns', [VehicleReturnController::class, 'index'])->name('admin.returns.index');
    Route::get('/fuel-loads', [App\Http\Controllers\FuelLoadController::class, 'index'])->name('fuel-loads.index');
    Route::post('/fuel-loads', [App\Http\Controllers\FuelLoadController::class, 'store'])->name('fuel-loads.store');

    // Notificaciones
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/mark-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAll');

    // Gestión de Activos
    Route::get('/assets/dashboard', [\App\Http\Controllers\AssetController::class, 'dashboard'])->name('assets.dashboard');
    Route::get('/assets/reports', [\App\Http\Controllers\AssetReportController::class, 'index'])->name('assets.reports.index');
    Route::get('/assets/reports/export', [\App\Http\Controllers\AssetReportController::class, 'export'])->name('assets.reports.export');
    Route::get('/assets/trash', [\App\Http\Controllers\AssetController::class, 'trash'])->name('assets.trash');
    Route::put('/assets/{id}/restore', [\App\Http\Controllers\AssetController::class, 'restore'])->name('assets.restore');
    Route::delete('/assets/{id}/force-delete', [\App\Http\Controllers\AssetController::class, 'forceDelete'])->name('assets.force-delete');
    Route::get('/assets/{id}/barcode', [\App\Http\Controllers\AssetController::class, 'downloadBarcode'])->name('assets.barcode');
    Route::post('/assets/barcodes/batch', [\App\Http\Controllers\AssetController::class, 'downloadBarcodes'])->name('assets.barcodes.batch');
    Route::post('/assets/{id}/assign', [\App\Http\Controllers\AssetController::class, 'assign'])->name('assets.assign');
    Route::put('/assets/{id}/cancel-assignment', [\App\Http\Controllers\AssetController::class, 'cancelAssignment'])->name('assets.cancel-assignment');
    Route::put('/assets/{id}/assignment/update', [\App\Http\Controllers\AssetController::class, 'updateAssignment'])->name('assets.update-assignment');
    Route::get('/assets/{id}/history', [\App\Http\Controllers\AssetController::class, 'history'])->name('assets.history');
    Route::get('/assets/{id}/history/pdf', [\App\Http\Controllers\AssetController::class, 'downloadHistoryPdf'])->name('assets.history.pdf');
    Route::get('/assets/export-pdf', [\App\Http\Controllers\AssetController::class, 'exportPdf'])->name('assets.export-pdf');

    // Rutas para resolver alertas de daños y mantenciones
    Route::post('/assets/{id}/maintenance', [\App\Http\Controllers\AssetController::class, 'sendToMaintenance'])->name('assets.maintenance.send');
    Route::post('/assets/{id}/maintenance/finish', [\App\Http\Controllers\AssetController::class, 'finishMaintenance'])->name('assets.maintenance.finish');
    Route::post('/assets/{id}/write-off', [\App\Http\Controllers\AssetController::class, 'writeOff'])->name('assets.write-off');

    Route::resource('assets', \App\Http\Controllers\AssetController::class);

    // Trabajadores Externos
    Route::get('/workers/check-rut', [\App\Http\Controllers\WorkerController::class, 'checkRut'])->name('workers.check-rut');
    Route::get('/workers/trash', [\App\Http\Controllers\WorkerController::class, 'trash'])->name('workers.trash');
    Route::post('/workers/{id}/restore', [\App\Http\Controllers\WorkerController::class, 'restore'])->name('workers.restore');
    Route::delete('/workers/{id}/force-delete', [\App\Http\Controllers\WorkerController::class, 'forceDelete'])->name('workers.force-delete');
    Route::resource('workers', \App\Http\Controllers\WorkerController::class);

});

// Rutas de cambio de contraseña forzado 
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ForceChangePasswordController::class, 'show'])
        ->name('password.change.notice');
    Route::post('/change-password', [ForceChangePasswordController::class, 'update'])
        ->name('password.change.update');
});

//
Route::middleware(['auth', 'force.password.change'])->group(function () {
    //gestion de usuarios
    Route::put('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::resource('users', UserController::class);


});

require __DIR__ . '/auth.php';