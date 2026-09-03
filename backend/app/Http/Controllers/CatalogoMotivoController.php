<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CatalogoMotivo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoMotivoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tipo = $request->query('tipo');
        $query = CatalogoMotivo::where('activo', true);

        if ($tipo) {
            $query->where(function($q) use ($tipo) {
                $q->where('tipo', $tipo)->orWhere('tipo', 'todos');
            });
        }

        $motivos = $query->orderBy('descripcion', 'asc')->get();
        return response()->json(['data' => $motivos]);
    }
}
