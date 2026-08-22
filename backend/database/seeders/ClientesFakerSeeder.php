<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ClientesFakerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('cliente2026');
        $faker = Faker::create('es_ES');

        $tiposNegocio = ['Minorista', 'Mediana Empresa'];

        for ($i = 0; $i < 100; $i++) {
            $nombre = $faker->firstName;
            $apellido = $faker->lastName;
            $email = strtolower($nombre . '.' . $apellido . $i . '@email.com'); // added index to ensure unique
            $razon_social = $faker->company . ' (' . $faker->randomElement($tiposNegocio) . ')';
            $ruc_cedula = $faker->numerify('18########001');
            $telefono = $faker->numerify('09########');
            $descripcion_dir = $faker->streetAddress . ', Ambato, Tungurahua';
            $referencia = 'Cerca de ' . $faker->word;
            
            // Ambato bounds approx: Lat -1.22 to -1.28, Lng -78.60 to -78.65
            $lat = $faker->randomFloat(4, -1.2800, -1.2200);
            $lng = $faker->randomFloat(4, -78.6500, -78.6000);

            // Insert into usuarios
            $usuarioId = DB::table('usuarios')->insertGetId([
                'nombre' => $nombre . ' ' . $apellido,
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
                'ruc_cedula' => $ruc_cedula,
                'razon_social' => $razon_social,
                'nombre_cliente' => $nombre . ' ' . $apellido,
                'telefono' => $telefono,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert into direcciones_cliente
            DB::table('direcciones_cliente')->insert([
                'cliente_id' => $clienteId,
                'descripcion' => $descripcion_dir,
                'referencia' => $referencia,
                'latitud' => $lat,
                'longitud' => $lng,
                'es_por_defecto' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
