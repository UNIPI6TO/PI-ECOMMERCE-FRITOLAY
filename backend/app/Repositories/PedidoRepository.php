<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\PedidoRepositoryInterface;
use App\Models\Pedido;
use Illuminate\Support\Collection;

class PedidoRepository implements PedidoRepositoryInterface
{
    public function create(array $data): Pedido
    {
        return Pedido::create($data);
    }

    public function findById(int $id): ?Pedido
    {
        return Pedido::with(['items.producto', 'cliente', 'direccion'])->find($id);
    }

    public function update(int $id, array $data): Pedido
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->update($data);
        return $pedido;
    }

    public function findByCliente(int $clienteId): Collection
    {
        return Pedido::where('cliente_id', $clienteId)
            ->with(['items'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function updateEstado(int $id, string $estado): bool
    {
        return (bool) Pedido::where('id', $id)->update(['estado' => $estado]);
    }

    public function getByEstado(string $estado, array $filtrosFecha = []): Collection
    {
        $query = Pedido::where('estado', $estado)->with(['cliente', 'direccion']);
        
        if (isset($filtrosFecha['desde']) && isset($filtrosFecha['hasta'])) {
            $query->whereBetween('creado_en', [$filtrosFecha['desde'], $filtrosFecha['hasta']]);
        }
        
        return $query->get();
    }

    public function getByEstados(array $estados, array $filtrosFecha = []): Collection
    {
        $query = Pedido::whereIn('estado', $estados)->with(['cliente', 'direccion']);
        
        if (isset($filtrosFecha['desde']) && isset($filtrosFecha['hasta'])) {
            $query->whereBetween('creado_en', [$filtrosFecha['desde'], $filtrosFecha['hasta']]);
        }
        
        return $query->get();
    }

    public function isAsignado(int $pedidoId): bool
    {
        $pedido = $this->findById($pedidoId);
        if (!$pedido) {
            return false;
        }
        
        $estadosAsignados = [
            Pedido::ESTADO_LISTO_PARA_ENTREGAR,
            Pedido::ESTADO_EN_RUTA,
            Pedido::ESTADO_ENTREGADO,
            Pedido::ESTADO_ENTREGADO_PARCIALMENTE
        ];
        
        return in_array($pedido->estado, $estadosAsignados, true);
    }

    public function countByEstado(): array
    {
        return Pedido::select('estado', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();
    }
}
