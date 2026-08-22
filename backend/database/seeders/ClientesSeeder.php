<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('cliente2026');

        $clientesData = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Lopez',
                'razon_social' => 'Tienda Minorista Lopez',
                'ruc_cedula' => '1801234567001',
                'telefono' => '0981234567',
                'descripcion_dir' => 'Calle Bolivar y Mera, Centro de Ambato, Tungurahua',
                'lat' => -1.2405,
                'lng' => -78.6256,
            ],
            [
                'nombre' => 'Maria',
                'apellido' => 'Gomez',
                'razon_social' => 'Abarrotes Gomez (Mediana Empresa)',
                'ruc_cedula' => '1807654321001',
                'telefono' => '0997654321',
                'descripcion_dir' => 'Av. Cevallos y Martinez, Ambato, Tungurahua',
                'lat' => -1.2415,
                'lng' => -78.6275,
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Perez',
                'razon_social' => 'Bazar Perez (Minorista)',
                'ruc_cedula' => '1802345678001',
                'telefono' => '0982345678',
                'descripcion_dir' => 'Izamba, Ambato, Tungurahua',
                'lat' => -1.2185,
                'lng' => -78.6081,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martinez',
                'razon_social' => 'Comercial Martinez (Mediana Empresa)',
                'ruc_cedula' => '1808765432001',
                'telefono' => '0998765432',
                'descripcion_dir' => 'Ficoa, Ambato, Tungurahua',
                'lat' => -1.2483,
                'lng' => -78.6361,
            ],
            [
                'nombre' => 'Jorge',
                'apellido' => 'Salazar',
                'razon_social' => 'Bodega Menor Salazar',
                'ruc_cedula' => '1803456789001',
                'telefono' => '0983456789',
                'descripcion_dir' => 'Huachi Chico, Ambato, Tungurahua',
                'lat' => -1.2721,
                'lng' => -78.6219,
            ]
        ];

        foreach ($clientesData as $data) {
            $email = strtolower($data['nombre'] . '.' . $data['apellido'] . '@email.com');

            // Insert into usuarios
            $usuarioId = DB::table('usuarios')->insertGetId([
                'nombre' => $data['nombre'] . ' ' . $data['apellido'],
                'email' => $email,
                'password_hash' => $password,
                'rol' => 'cliente',
                'activo' => true,
                'creado_en' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert into clientes
            $clienteId = DB::table('clientes')->insertGetId([
                'usuario_id' => $usuarioId,
                'ruc_cedula' => $data['ruc_cedula'],
                'razon_social' => $data['razon_social'],
                'nombre_cliente' => $data['nombre'] . ' ' . $data['apellido'],
                'telefono' => $data['telefono'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert into direcciones_cliente
            DB::table('direcciones_cliente')->insert([
                'cliente_id' => $clienteId,
                'descripcion' => $data['descripcion_dir'],
                'referencia' => 'Sin referencia',
                'latitud' => $data['lat'],
                'longitud' => $data['lng'],
                'es_por_defecto' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
