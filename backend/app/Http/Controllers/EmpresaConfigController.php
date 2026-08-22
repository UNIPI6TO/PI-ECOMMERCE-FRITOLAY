<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmpresaConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresaConfigController extends Controller
{
    /** GET /api/empresa — devuelve la configuración pública del emisor */
    public function show(): JsonResponse
    {
        $config = EmpresaConfig::get();
        if (!$config) {
            return response()->json(['error' => 'Configuración de empresa no encontrada'], 404);
        }
        return response()->json(['data' => $config]);
    }

    /** PUT /api/admin/empresa — actualiza la configuración (solo admin) */
    public function update(Request $request): JsonResponse
    {
        $config = EmpresaConfig::get();
        if (!$config) {
            return response()->json(['error' => 'Configuración de empresa no encontrada'], 404);
        }

        $validated = $request->validate([
            'razon_social'           => ['sometimes', 'string', 'max:200'],
            'nombre_comercial'       => ['sometimes', 'nullable', 'string', 'max:200'],
            'ruc'                    => ['sometimes', 'string', 'size:13'],
            'codigo_establecimiento' => ['sometimes', 'string', 'size:3'],
            'punto_emision'          => ['sometimes', 'string', 'size:3'],
            'direccion_matriz'       => ['sometimes', 'string', 'max:300'],
            'direccion_sucursal'     => ['sometimes', 'nullable', 'string', 'max:300'],
            'telefono'               => ['sometimes', 'nullable', 'string', 'max:20'],
            'email'                  => ['sometimes', 'nullable', 'email', 'max:100'],
            'tipo_contribuyente'     => ['sometimes', 'string', 'max:100'],
            'obligado_contabilidad'  => ['sometimes', 'boolean'],
            'tipo_ambiente'          => ['sometimes', 'in:1,2'],
            'tipo_emision'           => ['sometimes', 'in:1'],
            'logo_url'               => ['sometimes', 'nullable', 'string', 'max:500'],
            'color_primario'         => ['sometimes', 'string', 'max:7'],
        ]);

        $config->update($validated);
        return response()->json(['data' => $config->fresh()]);
    }
}
