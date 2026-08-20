<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DashboardFiltroRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function kpis(DashboardFiltroRequest $request)
    {
        return response()->json($this->dashboardService->getKpis($request->validated()));
    }

    public function ventas(DashboardFiltroRequest $request)
    {
        return response()->json($this->dashboardService->getVentas($request->validated()));
    }

    public function recaudacion(DashboardFiltroRequest $request)
    {
        return response()->json($this->dashboardService->getRecaudacion($request->validated()));
    }

    public function carritosAbandonados(DashboardFiltroRequest $request)
    {
        return response()->json($this->dashboardService->getCarritosAbandonados($request->validated()));
    }

    public function stock()
    {
        return response()->json($this->dashboardService->getStock());
    }
}
