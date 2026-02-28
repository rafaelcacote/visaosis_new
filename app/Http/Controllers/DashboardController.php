<?php

namespace App\Http\Controllers;

use App\Models\PedidoVenda;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $vendasPorMes = $this->getVendasPorMes();
        return view('dashboard', compact('vendasPorMes'));
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