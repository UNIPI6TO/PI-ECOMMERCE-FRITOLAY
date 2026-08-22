<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function store(ClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());
        return response()->json(['data' => $cliente], 201);
    }

    public function me(\Illuminate\Http\Request $request): JsonResponse
    {
        $cliente = Cliente::where('usuario_id', $request->input('user_id'))->first();
        if (!$cliente) {
            return response()->json(['data' => null]);
        }
        return response()->json(['data' => $cliente]);
    }

    public function update(ClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::updateOrCreate(
            ['usuario_id' => $request->input('user_id')],
            $request->validated()
        );
        return response()->json(['data' => $cliente]);
    }
}
