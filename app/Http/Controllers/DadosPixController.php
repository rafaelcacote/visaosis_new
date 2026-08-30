<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DadosPixController extends Controller
{
    public function edit()
    {
        $tenantId = $this->tenantIdOrAbort();
        $dadosPix = $this->findDadosPix($tenantId);

        return view('admin.dados-pix.edit', compact('dadosPix', 'tenantId'));
    }

    public function update(Request $request)
    {
        $tenantId = $this->tenantIdOrAbort();

        $validated = $request->validate([
            'tipo_chave' => 'required|string|in:CPF,CNPJ,EMAIL,TELEFONE,CELULAR,ALEATORIA',
            'chave' => 'required|string|max:255',
            'nome_titular' => 'required|string|max:255',
            'banco' => 'required|string|max:255',
        ], [
            'tipo_chave.required' => 'Selecione o tipo da chave PIX.',
            'tipo_chave.in' => 'Tipo de chave PIX inválido.',
            'chave.required' => 'Informe a chave PIX.',
            'nome_titular.required' => 'Informe o nome do titular.',
            'banco.required' => 'Informe o banco.',
        ]);

        try {
            $existing = $this->findDadosPix($tenantId);
            $payload = array_merge($validated, ['updated_at' => now()]);

            if ($existing) {
                DB::connection('cerberus')
                    ->table('seguranca.dados_pix')
                    ->where('tenant_id', $tenantId)
                    ->update($payload);
            } else {
                DB::connection('cerberus')
                    ->table('seguranca.dados_pix')
                    ->insert(array_merge($payload, [
                        'tenant_id' => $tenantId,
                        'created_at' => now(),
                    ]));
            }

            return redirect()
                ->route('admin.dados-pix.edit')
                ->with('success', 'Dados PIX atualizados com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar dados PIX: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Erro ao salvar os dados PIX. Tente novamente.'])
                ->withInput();
        }
    }

    private function tenantIdOrAbort(): int
    {
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            abort(403, 'Tenant não identificado na sessão.');
        }

        return (int) $tenantId;
    }

    private function findDadosPix(int $tenantId): ?object
    {
        return DB::connection('cerberus')
            ->table('seguranca.dados_pix')
            ->where('tenant_id', $tenantId)
            ->first();
    }
}
