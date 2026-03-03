# Plataforma de Gestión - Secretaría y Gerencia

Sistema web integral desarrollado para la centralización y administración operativa. Permite la gestión eficiente y auditada de la **flota de vehículos**, **solicitudes de recursos**, **activos e inventario** corporativo, y **salas de reuniones**, con un potente sistema de roles y reportes en PDF.

---

## 🚀 Módulos y Características Principales

### 🚙 Gestión de Vehículos y Flota
* **Catálogo de Vehículos**: Registro completo, información de patentes, modelos y estado general. Incluye papelera de reciclaje (Soft Deletes).
* **Reservas y Solicitudes**: Flujo de aprobación para solicitudes de vehículos (locales o fuera de la ciudad), con registro de acompañantes y justificación.
* **Mantenimiento**: Control de estado mecánico, ingreso a taller, historial de mantenimientos y alertas.
* **Combustible**: Registro detallado de cargas de combustible para control de consumo y eficiencia.
* **Documentación y Hojas de Ruta**: Archivo fotográfico de entrega y devolución del vehículo (Check-in/Check-out).

### 💻 Gestión de Activos (Equipos)
* **Inventario Centralizado**: Registro de activos por categoría, con panel de control (Dashboard) dinámico.
* **Asignaciones**: Entrega de equipos a personal interno (Usuarios) o externo (Trabajadores), registrando actas de entrega en PDF y respaldos fotográficos.
* **Etiquetado e Identificación**: Generación nativa de hojas con **Códigos de Barras** para trazabilidad física de los equipos.
* **Bajas y Daños**: Reporte fotográfico de activos en mal estado y proceso de dar de baja equipos obsoletos.

### 🏢 Gestión de Salas de Reuniones
* **Catálogo de Salas**: Visualización rápida de las capacidades y disponibilidad de salas de reunión.
* **Sistema de Agenda**: Agenda y reserva para personal de planta o solicitudes externas.
* **Reportes Mensuales**: Exportación de reportes de uso y ocupación de las instalaciones.

### 🛡️ Roles y Accesos (RBAC)
Sistema de múltiples niveles de autorización:
* **Admin**: Acceso y control total del sistema, auditorías, restauraciones de papelera y configuraciones críticas.
* **Supervisor**: Capacidad para aprobar o rechazar solicitudes operativas (vehículos, salas).
* **Driver / Worker**: Empleados regulares que pueden ver disponibilidad, solicitar vehículos, adjudicarse activos y revisar su *"Mis Reservas"*.
* **Viewer**: Modo de sólo vista para visualizar el estado general operativo.

---

## 🛠️ Stack Tecnológico

El proyecto está construido sobre un ecosistema moderno y robusto enfocado en la velocidad y escalabilidad.

* **Backend:** PHP 8.2+ / [Laravel 12.x](https://laravel.com/)
* **Frontend:** Blade Templates y [Livewire 4](https://livewire.laravel.com/) para interacciones dinámicas en tiempo real sin recargar página.
* **Base de Datos:** MySQL / MariaDB (o SQLite para entorno de desarrollo local).
* **Dependencias Clave:**
  * `barryvdh/laravel-dompdf`: Generación de actas, historiales y reportes en formato PDF.
  * `intervention/image`: Manipulación, optimización y recorte de fotografías subidas.
  * `picqer/php-barcode-generator`: Creación de códigos de barra para inventariado.

---

## ⚙️ Instrucciones de Instalación (Entorno Local)

Para ejecutar este proyecto en tu computadora, asegúrate de tener instalado PHP 8.2+, Composer, Node.js y un motor de base de datos.

1. **Clonar el repositorio y entrar al directorio:**
   ```bash
   git clone <url-del-repositorio>
   cd secretaria-gerencia
   ```

2. **Instalar dependencias de PHP y Node:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar el entorno:**
   Copia el archivo de ejemplo para crear tu propio `.env`.
   ```bash
   cp .env.example .env
   ```
   *Edita el archivo `.env` agregado tus credenciales de base de datos locales (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

4. **Generar la llave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Preparar la base de datos y Storage:**
   Ejecuta las migraciones y activa el enlace simbólico para poder ver las fotografías subidas.
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
   *(El flag `--seed` poblará la base de datos con categorías y datos de prueba si están disponibles).*

6. **Ejecutar el servidor de desarrollo:**
   Necesitarás dos consolas. Una para el servidor PHP y otra para compilar los recursos visuales Vite.
   ```bash
   # Consola 1:
   php artisan serve

   # Consola 2:
   npm run dev
   ```
   El sitio estará disponible en [http://localhost:8000](http://localhost:8000).

---

## ⏰ Tareas Programadas (Cron Jobs)

El sistema incluye comandos automatizados que revisan proactivamente las fechas importantes y envían alertas (Ej: *Vencimiento de Licencias de Conducir, Alertas de Mantenimiento, Expiración de Documentación de Vehículos*). 

En el servidor de producción, se debe añadir la siguiente línea al Crontab del usuario del sistema para que se ejecuten automáticamente:

```bash
* * * * * cd /ruta-a-tu-proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

*Desarrollado para el departamento de Secretaría y Gerencia.*
