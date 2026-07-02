<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EspecialidadeController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
        ];

        $query = Especialidade::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('descricao', 'ILIKE', "%{$search}%");
        }

        $especialidades = $query->orderBy('descricao')->paginate(15);

        return view('especialidades.index', compact('especialidades', 'filters'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Especialidade::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma especialidade com esta descrição.');
                    }
                },
            ],
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            Especialidade::create([
                'descricao' => $request->descricao,
                'ativo' => true,
            ]);

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar especialidade: ' . $e->getMessage());
        }
    }

    public function show(Especialidade $especialidade)
    {
        return view('especialidades.show', compact('especialidade'));
    }

    public function edit(Especialidade $especialidade)
    {
        return view('especialidades.edit', compact('especialidade'));
    }

    public function update(Request $request, Especialidade $especialidade)
    {
        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($especialidade) {
                    $exists = Especialidade::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->where('id', '!=', $especialidade->id)
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma especialidade com esta descrição.');
                    }
                },
            ],
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $especialidade->update([
                'descricao' => $request->descricao,
            ]);

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar especialidade: ' . $e->getMessage());
        }
    }

    public function destroy(Especialidade $especialidade)
    {
        try {
            DB::beginTransaction();

            $especialidade->delete();

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (
                strpos($e->getMessage(), 'Foreign key violation') !== false ||
                strpos($e->getMessage(), 'foreign key constraint') !== false
            ) {
                return back()->with('error', 'Não é possível excluir esta especialidade porque ela está sendo usada.');
            }

            return back()->with('error', 'Erro ao excluir especialidade: ' . $e->getMessage());
        }
    }
}
