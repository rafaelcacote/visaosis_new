<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\PedidoVenda;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $vendasPorMes = $this->getVendasPorMes();
        $clientesPorMes = $this->getClientesPorMes();
        $clientesUltimos12Meses = $this->getClientesUltimos12Meses();

        return view('dashboard', compact('vendasPorMes', 'clientesPorMes', 'clientesUltimos12Meses'));
    }

    private function getClientesPorMes(): array
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $inicio = Carbon::now()->subMonths(11)->startOfMonth();

        $query = Pessoa::query()
            ->where('created_at', '>=', $inicio);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (!empty($locationIds)) {
            $query->whereIn('location_id', $locationIds);
        } elseif ($locationId) {
            $query->where('location_id', $locationId);
        }

        $clientes = $query->get(['created_at']);

        $countByMonth = [];
        foreach ($clientes as $c) {
            $chave = $c->created_at?->format('Y-m');
            if (!$chave) {
                continue;
            }
            $countByMonth[$chave] = (int) (($countByMonth[$chave] ?? 0) + 1);
        }

        $meses = [];
        $valores = [];
        for ($i = 11; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            $chave = $data->format('Y-m');
            $meses[] = $data->locale('pt_BR')->translatedFormat('M');
            $valores[] = (int) ($countByMonth[$chave] ?? 0);
        }

        return [
            'labels' => $meses,
            'valores' => $valores,
            'total_atual' => array_sum($valores),
            'mes_anterior' => $valores[10] ?? 0,
            'mes_atual' => $valores[11] ?? 0,
        ];
    }

    private function getClientesUltimos12Meses(): int
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $inicio = Carbon::now()->subMonths(12)->startOfDay();

        $query = Pessoa::query()
            ->where('created_at', '>=', $inicio);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (!empty($locationIds)) {
            $query->whereIn('location_id', $locationIds);
        } elseif ($locationId) {
            $query->where('location_id', $locationId);
        }

        return (int) $query->count();
    }

    /**
     * Retorna vendas faturadas agrupadas por mês (últimos 12 meses)
     */
    private function getVendasPorMes(): array
    {
        $tenantId = session('tenant_id');
        $query = PedidoVenda::query()
            ->where('status', PedidoVenda::STATUS_FATURADO);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $inicio = Carbon::now()->subMonths(11)->startOfMonth();
        $pedidos = $query
            ->where('data_pedido', '>=', $inicio)
            ->get(['data_pedido', 'valor_total']);

        $vendas = [];
        foreach ($pedidos as $p) {
            $chave = $p->data_pedido->format('Y-m');
            $vendas[$chave] = ($vendas[$chave] ?? 0) + (float) $p->valor_total;
        }

        $meses = [];
        $valores = [];
        for ($i = 11; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            $chave = $data->format('Y-m');
            $meses[] = $data->locale('pt_BR')->translatedFormat('M');
            $valores[] = (float) ($vendas[$chave] ?? 0);
        }

        return [
            'labels' => $meses,
            'valores' => $valores,
            'total_atual' => array_sum($valores),
            'mes_anterior' => $valores[10] ?? 0,
            'mes_atual' => $valores[11] ?? 0,
        ];
    }
}
