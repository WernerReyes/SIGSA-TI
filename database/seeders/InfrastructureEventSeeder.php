<?php

namespace Database\Seeders;

use App\Models\InfrastructureEvents;
use Illuminate\Database\Seeder;
use Schema;

class InfrastructureEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        InfrastructureEvents::truncate();

        InfrastructureEvents::insert([
            [
                'title' => 'Cambio en configuración de red',
                'description' => 'Se aplicará un cambio en la configuración de los switches principales.',
                'type' => 'CHANGE',
                'date' => '2024-06-20 01:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Mantenimiento programado en servidor de correo',
                'description' => 'El servidor de correo estará fuera de servicio por mantenimiento.',
                'type' => 'MAINTENANCE',
                'date' => '2024-06-15 02:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Actualización de políticas de seguridad',
                'description' => 'Se implementarán nuevas reglas en el firewall.',
                'type' => 'SECURITY',
                'date' => '2024-07-01 23:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Auditoría interna de sistemas',
                'description' => 'Se revisarán los accesos y permisos de usuarios.',
                'type' => 'AUDIT',
                'date' => '2024-07-10 09:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Incidente en servidor web',
                'description' => 'El servidor web principal dejó de responder.',
                'type' => 'INCIDENT',
                'date' => '2024-07-15 14:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Renovación de certificados SSL',
                'description' => 'Se renovarán los certificados de seguridad de los sitios corporativos.',
                'type' => 'RENEWAL',
                'date' => '2024-07-20 22:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Cambio en configuración de VPN',
                'description' => 'Se actualizarán parámetros de conexión en la VPN corporativa.',
                'type' => 'CHANGE',
                'date' => '2024-08-01 03:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Mantenimiento en servidores de aplicaciones',
                'description' => 'Se reiniciarán los servidores de aplicaciones para aplicar mejoras.',
                'type' => 'MAINTENANCE',
                'date' => '2024-08-10 02:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Revisión de políticas de seguridad',
                'description' => 'Se validará la configuración de antivirus y antimalware.',
                'type' => 'SECURITY',
                'date' => '2024-08-15 11:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Auditoría externa de infraestructura',
                'description' => 'Auditores externos revisarán la infraestructura tecnológica.',
                'type' => 'AUDIT',
                'date' => '2024-08-20 10:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Incidente en sistema de almacenamiento',
                'description' => 'Se detectó una falla en el sistema SAN.',
                'type' => 'INCIDENT',
                'date' => '2024-08-25 15:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Renovación de licencias de software',
                'description' => 'Se renovarán las licencias de los sistemas críticos.',
                'type' => 'RENEWAL',
                'date' => '2024-09-01 09:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Cambio en configuración de base de datos',
                'description' => 'Se ajustarán parámetros de rendimiento en la base de datos.',
                'type' => 'CHANGE',
                'date' => '2024-09-05 01:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Mantenimiento en servidores de backup',
                'description' => 'Se revisarán los servidores de respaldo para optimizar rendimiento.',
                'type' => 'MAINTENANCE',
                'date' => '2024-09-10 02:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Actualización de reglas de seguridad',
                'description' => 'Se aplicarán nuevas reglas de acceso en el sistema.',
                'type' => 'SECURITY',
                'date' => '2024-09-15 12:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Auditoría de cumplimiento normativo',
                'description' => 'Se verificará el cumplimiento de normativas de seguridad.',
                'type' => 'AUDIT',
                'date' => '2024-09-20 10:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Incidente en servidor de autenticación',
                'description' => 'El servidor de autenticación dejó de responder.',
                'type' => 'INCIDENT',
                'date' => '2024-09-25 14:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Renovación de hardware de red',
                'description' => 'Se reemplazarán switches antiguos por nuevos equipos.',
                'type' => 'RENEWAL',
                'date' => '2024-10-01 08:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Cambio en configuración de servidores web',
                'description' => 'Se ajustarán parámetros de rendimiento en los servidores web.',
                'type' => 'CHANGE',
                'date' => '2024-10-05 01:00:00',
                'responsible_id' => 1,
            ],
            [
                'title' => 'Mantenimiento en sistema ERP',
                'description' => 'Se aplicarán mejoras al sistema ERP corporativo.',
                'type' => 'MAINTENANCE',
                'date' => '2024-10-10 02:00:00',
                'responsible_id' => 1,
            ],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
