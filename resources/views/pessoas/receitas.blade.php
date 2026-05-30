@extends('layouts.app')

@section('title', 'Receitas - ' . $pessoa->nome)

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-file-document-outline me-2"></i>
                Receitas - {{ $pessoa->nome }}
            </h2>
            <p class="text-muted mb-0">Histórico de receitas e cadastro de nova receita</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pessoas.show', $pessoa->id) }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-account-circle-outline me-2"></i>
                Paciente
            </a>
            <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $isEditing = isset($editPrescricao);
        $prescricaoForm = $isEditing ? $editPrescricao : null;
    @endphp

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="mdi {{ $isEditing ? 'mdi-pencil' : 'mdi-plus-circle-outline' }} me-2"></i>
                {{ $isEditing ? 'Editar Receita' : 'Nova Receita' }}
            </h5>
        </div>
        <div class="card-body">
            <form method="POST"
                action="{{ $isEditing ? route('pessoas.receitas.update', ['pessoa' => $pessoa->id, 'prescricao' => $prescricaoForm->id]) : route('pessoas.receitas.store', $pessoa->id) }}">
                @csrf
                @if ($isEditing)
                    @method('PATCH')
                @endif

                <div class="row g-3 mb-3">

                    <div class="col-md-9">
                        <label class="form-label">Nome do profissional que prescreveu</label>
                        <input type="text" class="form-control @error('especialista_externo') is-invalid @enderror"
                            name="especialista_externo"
                            value="{{ old('especialista_externo', $isEditing ? $prescricaoForm->especialista_externo : null) }}"
                            placeholder="Ex: Dr(a). Fulano de Tal">
                        @error('especialista_externo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center">Esférico</th>
                                <th class="text-center">Cilíndrico</th>
                                <th class="text-center">Eixo</th>
                                <th class="text-center">AV </th>
                                <th class="text-center">DNP</th>
                                <th class="text-center">Altura</th>
                                <th class="text-center">Adição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2" class="text-center fw-bold"
                                    style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.45);">
                                    LONGE
                                </td>
                                <td class="text-center fw-semibold" style="white-space: nowrap;">
                                    <i class="mdi mdi-eye-outline me-1"></i>OD
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_esferico') is-invalid @enderror"
                                        name="od_esferico"
                                        value="{{ old('od_esferico', $isEditing ? $prescricaoForm->esfera_od : null) }}"
                                        placeholder="+/-0.00">
                                    @error('od_esferico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_cilindrico') is-invalid @enderror"
                                        name="od_cilindrico"
                                        value="{{ old('od_cilindrico', $isEditing ? $prescricaoForm->cilindro_od : null) }}"
                                        placeholder="+/-0.00">
                                    @error('od_cilindrico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_eixo') is-invalid @enderror"
                                        name="od_eixo"
                                        value="{{ old('od_eixo', $isEditing ? $prescricaoForm->eixo_od : null) }}"
                                        placeholder="0-180">
                                    @error('od_eixo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_acuidade') is-invalid @enderror"
                                        name="od_acuidade"
                                        value="{{ old('od_acuidade', $isEditing ? $prescricaoForm->acuidade_od : null) }}"
                                        placeholder="20/20">
                                    @error('od_acuidade')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_dnp') is-invalid @enderror"
                                        name="od_dnp"
                                        value="{{ old('od_dnp', $isEditing ? $prescricaoForm->dnp_od : null) }}"
                                        placeholder="62">
                                    @error('od_dnp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_altura') is-invalid @enderror"
                                        name="od_altura"
                                        value="{{ old('od_altura', $isEditing ? $prescricaoForm->altura_od : null) }}"
                                        placeholder="0.00">
                                    @error('od_altura')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('od_adicao') is-invalid @enderror"
                                        name="od_adicao"
                                        value="{{ old('od_adicao', $isEditing ? $prescricaoForm->adicao_od : null) }}"
                                        placeholder="+0.00">
                                    @error('od_adicao')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center fw-semibold" style="white-space: nowrap;">
                                    <i class="mdi mdi-eye-outline me-1"></i>OE
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_esferico') is-invalid @enderror"
                                        name="oe_esferico"
                                        value="{{ old('oe_esferico', $isEditing ? $prescricaoForm->esfera_oe : null) }}"
                                        placeholder="+/-0.00">
                                    @error('oe_esferico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_cilindrico') is-invalid @enderror"
                                        name="oe_cilindrico"
                                        value="{{ old('oe_cilindrico', $isEditing ? $prescricaoForm->cilindro_oe : null) }}"
                                        placeholder="+/-0.00">
                                    @error('oe_cilindrico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_eixo') is-invalid @enderror"
                                        name="oe_eixo"
                                        value="{{ old('oe_eixo', $isEditing ? $prescricaoForm->eixo_oe : null) }}"
                                        placeholder="0-180">
                                    @error('oe_eixo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_acuidade') is-invalid @enderror"
                                        name="oe_acuidade"
                                        value="{{ old('oe_acuidade', $isEditing ? $prescricaoForm->acuidade_oe : null) }}"
                                        placeholder="20/20">
                                    @error('oe_acuidade')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_dnp') is-invalid @enderror"
                                        name="oe_dnp"
                                        value="{{ old('oe_dnp', $isEditing ? $prescricaoForm->dnp_oe : null) }}"
                                        placeholder="62">
                                    @error('oe_dnp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_altura') is-invalid @enderror"
                                        name="oe_altura"
                                        value="{{ old('oe_altura', $isEditing ? $prescricaoForm->altura_oe : null) }}"
                                        placeholder="0.00">
                                    @error('oe_altura')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control @error('oe_adicao') is-invalid @enderror"
                                        name="oe_adicao"
                                        value="{{ old('oe_adicao', $isEditing ? $prescricaoForm->adicao_oe : null) }}"
                                        placeholder="+0.00">
                                    @error('oe_adicao')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">

                        <tbody>
                            <tr>
                                <td rowspan="2" class="text-center fw-bold"
                                    style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.45);">
                                    PERTO
                                </td>
                                <td class="text-center fw-semibold" style="white-space: nowrap;">
                                    <i class="mdi mdi-eye-outline me-1"></i>OD
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_esferico_perto') is-invalid @enderror"
                                        name="od_esferico_perto"
                                        value="{{ old('od_esferico_perto', $isEditing ? $prescricaoForm->esfera_od_perto : null) }}"
                                        placeholder="+/-0.00">
                                    @error('od_esferico_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_cilindrico_perto') is-invalid @enderror"
                                        name="od_cilindrico_perto"
                                        value="{{ old('od_cilindrico_perto', $isEditing ? $prescricaoForm->cilindro_od_perto : null) }}"
                                        placeholder="+/-0.00">
                                    @error('od_cilindrico_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_eixo_perto') is-invalid @enderror"
                                        name="od_eixo_perto"
                                        value="{{ old('od_eixo_perto', $isEditing ? $prescricaoForm->eixo_od_perto : null) }}"
                                        placeholder="0-180">
                                    @error('od_eixo_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_acuidade_perto') is-invalid @enderror"
                                        name="od_acuidade_perto"
                                        value="{{ old('od_acuidade_perto', $isEditing ? $prescricaoForm->acuidade_od_perto : null) }}"
                                        placeholder="20/20">
                                    @error('od_acuidade_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_dnp_perto') is-invalid @enderror"
                                        name="od_dnp_perto"
                                        value="{{ old('od_dnp_perto', $isEditing ? $prescricaoForm->dnp_od_perto : null) }}"
                                        placeholder="62">
                                    @error('od_dnp_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_altura_perto') is-invalid @enderror"
                                        name="od_altura_perto"
                                        value="{{ old('od_altura_perto', $isEditing ? $prescricaoForm->altura_od_perto : null) }}"
                                        placeholder="0.00">
                                    @error('od_altura_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('od_adicao_perto') is-invalid @enderror"
                                        name="od_adicao_perto"
                                        value="{{ old('od_adicao_perto', $isEditing ? $prescricaoForm->adicao_od_perto : null) }}"
                                        placeholder="+0.00">
                                    @error('od_adicao_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center fw-semibold" style="white-space: nowrap;">
                                    <i class="mdi mdi-eye-outline me-1"></i>OE
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_esferico_perto') is-invalid @enderror"
                                        name="oe_esferico_perto"
                                        value="{{ old('oe_esferico_perto', $isEditing ? $prescricaoForm->esfera_oe_perto : null) }}"
                                        placeholder="+/-0.00">
                                    @error('oe_esferico_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_cilindrico_perto') is-invalid @enderror"
                                        name="oe_cilindrico_perto"
                                        value="{{ old('oe_cilindrico_perto', $isEditing ? $prescricaoForm->cilindro_oe_perto : null) }}"
                                        placeholder="+/-0.00">
                                    @error('oe_cilindrico_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_eixo_perto') is-invalid @enderror"
                                        name="oe_eixo_perto"
                                        value="{{ old('oe_eixo_perto', $isEditing ? $prescricaoForm->eixo_oe_perto : null) }}"
                                        placeholder="0-180">
                                    @error('oe_eixo_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_acuidade_perto') is-invalid @enderror"
                                        name="oe_acuidade_perto"
                                        value="{{ old('oe_acuidade_perto', $isEditing ? $prescricaoForm->acuidade_oe_perto : null) }}"
                                        placeholder="20/20">
                                    @error('oe_acuidade_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_dnp_perto') is-invalid @enderror"
                                        name="oe_dnp_perto"
                                        value="{{ old('oe_dnp_perto', $isEditing ? $prescricaoForm->dnp_oe_perto : null) }}"
                                        placeholder="62">
                                    @error('oe_dnp_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_altura_perto') is-invalid @enderror"
                                        name="oe_altura_perto"
                                        value="{{ old('oe_altura_perto', $isEditing ? $prescricaoForm->altura_oe_perto : null) }}"
                                        placeholder="0.00">
                                    @error('oe_altura_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <input type="text"
                                        class="form-control @error('oe_adicao_perto') is-invalid @enderror"
                                        name="oe_adicao_perto"
                                        value="{{ old('oe_adicao_perto', $isEditing ? $prescricaoForm->adicao_oe_perto : null) }}"
                                        placeholder="+0.00">
                                    @error('oe_adicao_perto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Lente</label>
                        @php $tipo = old('tipo_lente', $isEditing ? $prescricaoForm->tipo_lente : null) @endphp
                        <select class="form-select @error('tipo_lente') is-invalid @enderror" name="tipo_lente">
                            <option value="">Selecione</option>
                            <option value="Monofocal" {{ $tipo === 'Monofocal' ? 'selected' : '' }}>Monofocal</option>
                            <option value="Bifocal" {{ $tipo === 'Bifocal' ? 'selected' : '' }}>Bifocal</option>
                            <option value="Multifocal" {{ $tipo === 'Multifocal' ? 'selected' : '' }}>Multifocal</option>
                        </select>
                        @error('tipo_lente')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Validade</label>
                        @php $val = (string) old('validade_dias', $isEditing ? $prescricaoForm->validade_dias : '') @endphp
                        <select class="form-select @error('validade_dias') is-invalid @enderror" name="validade_dias">
                            <option value="">Selecione</option>
                            <option value="365" {{ $val === '365' ? 'selected' : '' }}>1 Ano</option>
                            <option value="180" {{ $val === '180' ? 'selected' : '' }}>6 Meses</option>

                        </select>
                        @error('validade_dias')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Diagnóstico</label>
                        <input type="text" class="form-control @error('diagnostico') is-invalid @enderror"
                            name="diagnostico"
                            value="{{ old('diagnostico', $isEditing ? $prescricaoForm->diagnostico : null) }}">
                        @error('diagnostico')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Recomendações</label>
                        <textarea class="form-control @error('recomendacoes') is-invalid @enderror" name="recomendacoes" rows="2">{{ old('recomendacoes', $isEditing ? $prescricaoForm->recomendacoes : null) }}</textarea>
                        @error('recomendacoes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Observações da Receita</label>
                        <textarea class="form-control @error('observacoes_receita') is-invalid @enderror" name="observacoes_receita"
                            rows="2">{{ old('observacoes_receita', $isEditing ? $prescricaoForm->observacoes : null) }}</textarea>
                        @error('observacoes_receita')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    @if ($isEditing)
                        <a href="{{ route('pessoas.receitas', $pessoa->id) }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-close me-2"></i>
                            Cancelar
                        </a>
                    @endif
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save me-2"></i>
                        {{ $isEditing ? 'Salvar Alterações' : 'Salvar Receita' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="mdi mdi-history me-2"></i>
                Histórico
            </h5>
        </div>
        <div class="card-body">
            @if ($prescricoes->isEmpty())
                <div class="text-muted">Nenhuma receita cadastrada para este paciente.</div>
            @else
                @php
                    $now = \Carbon\Carbon::now();

                    $historyItems = $prescricoes->values()->map(function ($prescricao) use ($pessoa, $now) {
                        $createdAt = $prescricao->created_at ? $prescricao->created_at->copy() : null;
                        $validadeDias = $prescricao->validade_dias ? (int) $prescricao->validade_dias : null;
                        $validUntil = $createdAt && $validadeDias ? $createdAt->copy()->addDays($validadeDias) : null;

                        $daysToExpire = null;
                        if ($validUntil) {
                            $daysToExpire = $now->startOfDay()->diffInDays($validUntil->startOfDay(), false);
                        }

                        $isExpired = $daysToExpire !== null ? $daysToExpire < 0 : false;
                        $statusLabel = $daysToExpire === null ? 'Sem validade' : ($isExpired ? 'Vencida' : 'Em dia');
                        $statusVariant = $daysToExpire === null ? 'secondary' : ($isExpired ? 'danger' : 'primary');

                        $validText = '-';
                        if ($daysToExpire !== null) {
                            $absDays = abs($daysToExpire);
                            $validText = $daysToExpire >= 0 ? "Vence em {$absDays} dias" : "Venceu há {$absDays} dias";
                        }

                        $profNome =
                            $prescricao->consulta && $prescricao->consulta->profissional
                                ? $prescricao->consulta->profissional->nome ?? ''
                                : $prescricao->especialista_externo ?? '';

                        $profDisplay = $profNome ?: 'Não informado';
                        $profType = $prescricao->especialista_externo ? 'Especialista Externo' : 'Interno/Sistema';

                        return [
                            'id' => $prescricao->id,
                            'data' => $createdAt ? $createdAt->format('d/m/Y') : '-',
                            'data_hora' => $createdAt ? $createdAt->format('d/m/Y H:i') : '-',
                            'status_label' => $statusLabel,
                            'status_variant' => $statusVariant,
                            'validade_dias' => $validadeDias,
                            'valida_ate' => $validUntil ? $validUntil->format('d/m/Y') : '-',
                            'validade_texto' => $validText,
                            'profissional' => $profDisplay,
                            'profissional_tipo' => $profType,
                            'tipo_lente' => $prescricao->tipo_lente ?: '-',
                            'diagnostico' => $prescricao->diagnostico ?: '-',
                            'recomendacoes' => $prescricao->recomendacoes ?: '-',
                            'observacoes' => $prescricao->observacoes ?: '-',
                            'longe' => [
                                'od' => [
                                    'esferico' => $prescricao->esfera_od ?: '-',
                                    'cilindrico' => $prescricao->cilindro_od ?? '-',
                                    'eixo' => $prescricao->eixo_od ?? '-',
                                    'av' => $prescricao->acuidade_od ?: '-',
                                    'dnp' => $prescricao->dnp_od ?? '-',
                                    'altura' => $prescricao->altura_od ?? '-',
                                    'adicao' => $prescricao->adicao_od ?? '-',
                                ],
                                'oe' => [
                                    'esferico' => $prescricao->esfera_oe ?: '-',
                                    'cilindrico' => $prescricao->cilindro_oe ?? '-',
                                    'eixo' => $prescricao->eixo_oe ?? '-',
                                    'av' => $prescricao->acuidade_oe ?: '-',
                                    'dnp' => $prescricao->dnp_oe ?? '-',
                                    'altura' => $prescricao->altura_oe ?? '-',
                                    'adicao' => $prescricao->adicao_oe ?? '-',
                                ],
                            ],
                            'perto' => [
                                'od' => [
                                    'esferico' => $prescricao->esfera_od_perto ?: '-',
                                    'cilindrico' => $prescricao->cilindro_od_perto ?? '-',
                                    'eixo' => $prescricao->eixo_od_perto ?? '-',
                                    'av' => $prescricao->acuidade_od_perto ?: '-',
                                    'dnp' => $prescricao->dnp_od_perto ?? '-',
                                    'altura' => $prescricao->altura_od_perto ?? '-',
                                    'adicao' => $prescricao->adicao_od_perto ?? '-',
                                ],
                                'oe' => [
                                    'esferico' => $prescricao->esfera_oe_perto ?: '-',
                                    'cilindrico' => $prescricao->cilindro_oe_perto ?? '-',
                                    'eixo' => $prescricao->eixo_oe_perto ?? '-',
                                    'av' => $prescricao->acuidade_oe_perto ?: '-',
                                    'dnp' => $prescricao->dnp_oe_perto ?? '-',
                                    'altura' => $prescricao->altura_oe_perto ?? '-',
                                    'adicao' => $prescricao->adicao_oe_perto ?? '-',
                                ],
                            ],
                            'can_edit' => (bool) $prescricao->especialista_externo,
                            'edit_url' => $prescricao->especialista_externo
                                ? route('pessoas.receitas.edit', [
                                    'pessoa' => $pessoa->id,
                                    'prescricao' => $prescricao->id,
                                ])
                                : null,
                        ];
                    });

                    $expiredCount = $historyItems->where('status_variant', 'danger')->count();
                    $initialHistoryId = isset($editPrescricao) ? $editPrescricao->id : null;
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-semibold">
                        <span class="text-muted">Quantidade de Receitas</span>
                        <span class="ms-2" id="rxCount">{{ $historyItems->count() }}</span>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Receitas Vencidas</div>
                        <div class="fw-semibold text-danger" id="rxExpiredCount">{{ $expiredCount }}</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="rxPrevBtn">
                        <i class="mdi mdi-chevron-left"></i>
                        Anterior
                    </button>
                    <div class="text-muted small">
                        <span id="rxPosition">1</span> de <span id="rxTotal">{{ $historyItems->count() }}</span>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="rxNextBtn">
                        Próxima
                        <i class="mdi mdi-chevron-right"></i>
                    </button>
                </div>

                <div class="border rounded p-3" id="rxCard">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="rounded p-3 text-white h-100" id="rxStatusBox">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="small opacity-75">Status</div>
                                        <div class="fs-4 fw-semibold" id="rxStatusLabel">-</div>
                                    </div>
                                    <div class="fs-2">
                                        <i class="mdi mdi-flag-variant-outline"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rounded p-3 text-white h-100" id="rxValidityBox">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="small opacity-75">Validade</div>
                                        <div class="fs-4 fw-semibold" id="rxValidUntilDate">-</div>
                                    </div>
                                    <div class="fs-2">
                                        <i class="mdi mdi-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <span class="text-muted small">Médico / Optometrista:</span>
                                <span class="fw-semibold ms-2" id="rxProfessional">-</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-muted small">Data:</span>
                                <span class="ms-2" id="rxDate">-</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-muted small">Válida até:</span>
                                <span class="ms-2" id="rxValidUntilText">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Observação</label>
                            <input type="text" class="form-control" id="rxObservation" readonly>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 44px;"></th>
                                    <th style="width: 90px;"></th>
                                    <th class="text-center">Esférico</th>
                                    <th class="text-center">Cilíndrico</th>
                                    <th class="text-center">Eixo</th>
                                    <th class="text-center">AV</th>
                                    <th class="text-center">DNP</th>
                                    <th class="text-center">Altura</th>
                                    <th class="text-center">Adição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold"
                                        style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.45);">
                                        LONGE
                                    </td>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OD
                                    </td>
                                    <td class="text-center" id="rxLongeOdEsferico">-</td>
                                    <td class="text-center" id="rxLongeOdCilindrico">-</td>
                                    <td class="text-center" id="rxLongeOdEixo">-</td>
                                    <td class="text-center" id="rxLongeOdAv">-</td>
                                    <td class="text-center" id="rxLongeOdDnp">-</td>
                                    <td class="text-center" id="rxLongeOdAltura">-</td>
                                    <td class="text-center" id="rxLongeOdAdicao">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OE
                                    </td>
                                    <td class="text-center" id="rxLongeOeEsferico">-</td>
                                    <td class="text-center" id="rxLongeOeCilindrico">-</td>
                                    <td class="text-center" id="rxLongeOeEixo">-</td>
                                    <td class="text-center" id="rxLongeOeAv">-</td>
                                    <td class="text-center" id="rxLongeOeDnp">-</td>
                                    <td class="text-center" id="rxLongeOeAltura">-</td>
                                    <td class="text-center" id="rxLongeOeAdicao">-</td>
                                </tr>
                                <tr>
                                    <td rowspan="2" class="text-center fw-bold"
                                        style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.45);">
                                        PERTO
                                    </td>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OD
                                    </td>
                                    <td class="text-center" id="rxPertoOdEsferico">-</td>
                                    <td class="text-center" id="rxPertoOdCilindrico">-</td>
                                    <td class="text-center" id="rxPertoOdEixo">-</td>
                                    <td class="text-center" id="rxPertoOdAv">-</td>
                                    <td class="text-center" id="rxPertoOdDnp">-</td>
                                    <td class="text-center" id="rxPertoOdAltura">-</td>
                                    <td class="text-center" id="rxPertoOdAdicao">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OE
                                    </td>
                                    <td class="text-center" id="rxPertoOeEsferico">-</td>
                                    <td class="text-center" id="rxPertoOeCilindrico">-</td>
                                    <td class="text-center" id="rxPertoOeEixo">-</td>
                                    <td class="text-center" id="rxPertoOeAv">-</td>
                                    <td class="text-center" id="rxPertoOeDnp">-</td>
                                    <td class="text-center" id="rxPertoOeAltura">-</td>
                                    <td class="text-center" id="rxPertoOeAdicao">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Diagnóstico</div>
                            <div id="rxDiagnostico">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Recomendações</div>
                            <div id="rxRecomendacoes">-</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Tipo / Origem</div>
                            <div class="fw-semibold" id="rxTipoLente">-</div>
                            <div class="text-muted small" id="rxProfessionalType">-</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="#" class="btn btn-outline-primary" id="rxEditBtn" style="display: none;">
                            <i class="mdi mdi-pencil me-2"></i>
                            Editar Receita
                        </a>
                        <button type="button" class="btn btn-outline-secondary" id="rxPrintBtn">
                            <i class="mdi mdi-printer me-2"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {
            const historyItems = @json($historyItems ?? []);
            const initialId = @json($initialHistoryId ?? null);

            const totalEl = document.getElementById('rxTotal');
            const positionEl = document.getElementById('rxPosition');
            const prevBtn = document.getElementById('rxPrevBtn');
            const nextBtn = document.getElementById('rxNextBtn');

            const statusBox = document.getElementById('rxStatusBox');
            const statusLabelEl = document.getElementById('rxStatusLabel');
            const validityBox = document.getElementById('rxValidityBox');
            const validUntilDateEl = document.getElementById('rxValidUntilDate');

            const professionalEl = document.getElementById('rxProfessional');
            const professionalTypeEl = document.getElementById('rxProfessionalType');
            const dateEl = document.getElementById('rxDate');
            const validUntilTextEl = document.getElementById('rxValidUntilText');
            const observationEl = document.getElementById('rxObservation');

            const editBtn = document.getElementById('rxEditBtn');
            const printBtn = document.getElementById('rxPrintBtn');

            const byId = (id) => document.getElementById(id);

            const setText = (id, value) => {
                const el = byId(id);
                if (!el) return;
                el.textContent = value == null || value === '' ? '-' : String(value);
            };

            const setBoxVariant = (el, variant) => {
                if (!el) return;
                el.classList.remove('bg-primary', 'bg-danger', 'bg-secondary', 'bg-info', 'bg-success',
                    'bg-warning',
                    'text-dark');
                if (variant === 'warning') {
                    el.classList.add('bg-warning', 'text-dark');
                    return;
                }
                el.classList.add('bg-' + (variant || 'primary'));
            };

            let index = 0;
            if (initialId) {
                const found = historyItems.findIndex((x) => String(x.id) === String(initialId));
                if (found >= 0) index = found;
            }

            const render = () => {
                const total = historyItems.length;
                if (totalEl) totalEl.textContent = String(total);
                if (positionEl) positionEl.textContent = String(index + 1);

                const item = historyItems[index];
                if (!item) return;

                setBoxVariant(statusBox, item.status_variant);
                setBoxVariant(validityBox, item.status_variant);

                if (statusLabelEl) statusLabelEl.textContent = item.status_label || '-';
                if (validUntilDateEl) validUntilDateEl.textContent = item.valida_ate || '-';

                if (professionalEl) professionalEl.textContent = item.profissional || '-';
                if (professionalTypeEl) professionalTypeEl.textContent = item.profissional_tipo || '-';
                if (dateEl) dateEl.textContent = item.data_hora || '-';
                if (validUntilTextEl) validUntilTextEl.textContent = item.validade_texto || '-';
                if (observationEl) observationEl.value = item.observacoes || '-';

                setText('rxLongeOdEsferico', item.longe?.od?.esferico);
                setText('rxLongeOdCilindrico', item.longe?.od?.cilindrico);
                setText('rxLongeOdEixo', item.longe?.od?.eixo);
                setText('rxLongeOdAv', item.longe?.od?.av);
                setText('rxLongeOdDnp', item.longe?.od?.dnp);
                setText('rxLongeOdAltura', item.longe?.od?.altura);
                setText('rxLongeOdAdicao', item.longe?.od?.adicao);

                setText('rxLongeOeEsferico', item.longe?.oe?.esferico);
                setText('rxLongeOeCilindrico', item.longe?.oe?.cilindrico);
                setText('rxLongeOeEixo', item.longe?.oe?.eixo);
                setText('rxLongeOeAv', item.longe?.oe?.av);
                setText('rxLongeOeDnp', item.longe?.oe?.dnp);
                setText('rxLongeOeAltura', item.longe?.oe?.altura);
                setText('rxLongeOeAdicao', item.longe?.oe?.adicao);

                setText('rxPertoOdEsferico', item.perto?.od?.esferico);
                setText('rxPertoOdCilindrico', item.perto?.od?.cilindrico);
                setText('rxPertoOdEixo', item.perto?.od?.eixo);
                setText('rxPertoOdAv', item.perto?.od?.av);
                setText('rxPertoOdDnp', item.perto?.od?.dnp);
                setText('rxPertoOdAltura', item.perto?.od?.altura);
                setText('rxPertoOdAdicao', item.perto?.od?.adicao);

                setText('rxPertoOeEsferico', item.perto?.oe?.esferico);
                setText('rxPertoOeCilindrico', item.perto?.oe?.cilindrico);
                setText('rxPertoOeEixo', item.perto?.oe?.eixo);
                setText('rxPertoOeAv', item.perto?.oe?.av);
                setText('rxPertoOeDnp', item.perto?.oe?.dnp);
                setText('rxPertoOeAltura', item.perto?.oe?.altura);
                setText('rxPertoOeAdicao', item.perto?.oe?.adicao);

                setText('rxDiagnostico', item.diagnostico);
                setText('rxRecomendacoes', item.recomendacoes);
                setText('rxTipoLente', item.tipo_lente);

                if (editBtn) {
                    if (item.can_edit && item.edit_url) {
                        editBtn.style.display = '';
                        editBtn.href = item.edit_url;
                    } else {
                        editBtn.style.display = 'none';
                        editBtn.href = '#';
                    }
                }

                if (prevBtn) prevBtn.disabled = index <= 0;
                if (nextBtn) nextBtn.disabled = index >= total - 1;
            };

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    if (index > 0) {
                        index -= 1;
                        render();
                    }
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    if (index < historyItems.length - 1) {
                        index += 1;
                        render();
                    }
                });
            }

            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            render();
        })();
    </script>
@endpush
