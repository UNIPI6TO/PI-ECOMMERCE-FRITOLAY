<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductoIndexRequest;
use App\Services\ProductoService;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function __construct(private readonly ProductoService $productoService)
    {
    }

    public function index(ProductoIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $orderBy = $filters['order_by'] ?? 'nombre';
        $orden = $filters['orden'] ?? 'asc';
        unset($filters['order_by'], $filters['orden']);
        
        $productos = $this->productoService->getCatalogo($filters, $orderBy, $orden);
        
        return response()->json(['data' => $productos]);
    }

    public function show(int $id): JsonResponse
    {
        $producto = $this->productoService->getProducto($id);
        
        return response()->json(['data' => $producto]);
    }

    public function stock(int $id): JsonResponse
    {
        $stock = $this->productoService->getStock($id);
        
        return response()->json(['data' => ['stock' => $stock]]);
    }
}
