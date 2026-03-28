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
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Profissional</th>
                                <th class="text-center">OD Esf.</th>
                                <th class="text-center">OD Cil.</th>
                                <th class="text-center">OD Eixo</th>
                                <th class="text-center">OD DNP</th>
                                <th class="text-center">OD Alt.</th>
                                <th class="text-center">OD Adi.</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">OE Esf.</th>
                                <th class="text-center">OE Cil.</th>
                                <th class="text-center">OE Eixo</th>
                                <th class="text-center">OE DNP</th>
                                <th class="text-center">OE Alt.</th>
                                <th class="text-center">OE Adi.</th>
                                <th class="text-center">Validade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prescricoes as $prescricao)
                                <tr>
                                    <td style="white-space: nowrap;">
                                        {{ $prescricao->created_at ? $prescricao->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $profNome =
                                                $prescricao->consulta && $prescricao->consulta->profissional
                                                    ? $prescricao->consulta->profissional->nome ?? ''
                                                    : $prescricao->especialista_externo ?? '';
                                        @endphp
                                        <div class="fw-semibold">{{ $profNome ?: 'Não informado' }}</div>
                                        <small class="text-muted">
                                            {{ $prescricao->especialista_externo ?? false ? 'Especialista Externo' : 'Interno/Sistema' }}
                                        </small>
                                    </td>
                                    <td class="text-center">{{ $prescricao->esfera_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->cilindro_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->eixo_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->dnp_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->altura_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->adicao_od ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->tipo_lente ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->esfera_oe ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->cilindro_oe ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->eixo_oe ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->dnp_oe ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->altura_oe ?? '-' }}</td>
                                    <td class="text-center">{{ $prescricao->adicao_oe ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($prescricao->validade_dias)
                                            {{ $prescricao->validade_dias }} dias
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="16" class="bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="text-muted small">Diagnóstico</div>
                                                <div>{{ $prescricao->diagnostico ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-muted small">Recomendações</div>
                                                <div>{{ $prescricao->recomendacoes ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-muted small">Observações da Receita</div>
                                                <div>{{ $prescricao->observacoes ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="mdi mdi-plus-circle-outline me-2"></i>
                Nova Receita
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('pessoas.receitas.store', $pessoa->id) }}">
                @csrf

                <div class="row g-3 mb-3">

                    <div class="col-md-9">
                        <label class="form-label">Nome do profissional que prescreveu</label>
                        <input type="text" class="form-control @error('especialista_externo') is-invalid @enderror"
                            name="especialista_externo" value="{{ old('especialista_externo') }}"
                            placeholder="Ex: Dr(a). Fulano de Tal">
                        @error('especialista_externo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width: 11%">OD Esférico</th>
                                <th style="width: 11%">OD Cilíndrico</th>
                                <th style="width: 10%">OD Eixo</th>
                                <th style="width: 10%">OD DNP</th>
                                <th style="width: 10%">OD Altura</th>
                                <th style="width: 10%">OD Adição</th>
                                <th style="width: 18%">Tipo de Lente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control @error('od_esferico') is-invalid @enderror"
                                        name="od_esferico" value="{{ old('od_esferico') }}" placeholder="+/-0.00">
                                    @error('od_esferico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('od_cilindrico') is-invalid @enderror"
                                        name="od_cilindrico" value="{{ old('od_cilindrico') }}" placeholder="+/-0.00">
                                    @error('od_cilindrico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('od_eixo') is-invalid @enderror"
                                        name="od_eixo" value="{{ old('od_eixo') }}" placeholder="0-180">
                                    @error('od_eixo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('od_dnp') is-invalid @enderror"
                                        name="od_dnp" value="{{ old('od_dnp') }}" placeholder="62">
                                    @error('od_dnp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('od_altura') is-invalid @enderror"
                                        name="od_altura" value="{{ old('od_altura') }}" placeholder="0.00">
                                    @error('od_altura')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('od_adicao') is-invalid @enderror"
                                        name="od_adicao" value="{{ old('od_adicao') }}" placeholder="+0.00">
                                    @error('od_adicao')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    @php $tipo = old('tipo_lente') @endphp
                                    <select class="form-select @error('tipo_lente') is-invalid @enderror"
                                        name="tipo_lente">
                                        <option value="">Selecione</option>
                                        <option value="Monofocal" {{ $tipo === 'Monofocal' ? 'selected' : '' }}>Monofocal
                                        </option>
                                        <option value="Bifocal" {{ $tipo === 'Bifocal' ? 'selected' : '' }}>Bifocal
                                        </option>
                                        <option value="Multifocal" {{ $tipo === 'Multifocal' ? 'selected' : '' }}>
                                            Multifocal</option>
                                    </select>
                                    @error('tipo_lente')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width: 11%">OE Esférico</th>
                                <th style="width: 11%">OE Cilíndrico</th>
                                <th style="width: 10%">OE Eixo</th>
                                <th style="width: 10%">OE DNP</th>
                                <th style="width: 10%">OE Altura</th>
                                <th style="width: 10%">OE Adição</th>
                                <th style="width: 18%">Validade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control @error('oe_esferico') is-invalid @enderror"
                                        name="oe_esferico" value="{{ old('oe_esferico') }}" placeholder="+/-0.00">
                                    @error('oe_esferico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error('oe_cilindrico') is-invalid @enderror"
                                        name="oe_cilindrico" value="{{ old('oe_cilindrico') }}" placeholder="+/-0.00">
                                    @error('oe_cilindrico')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('oe_eixo') is-invalid @enderror"
                                        name="oe_eixo" value="{{ old('oe_eixo') }}" placeholder="0-180">
                                    @error('oe_eixo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('oe_dnp') is-invalid @enderror"
                                        name="oe_dnp" value="{{ old('oe_dnp') }}" placeholder="62">
                                    @error('oe_dnp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('oe_altura') is-invalid @enderror"
                                        name="oe_altura" value="{{ old('oe_altura') }}" placeholder="0.00">
                                    @error('oe_altura')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control @error('oe_adicao') is-invalid @enderror"
                                        name="oe_adicao" value="{{ old('oe_adicao') }}" placeholder="+0.00">
                                    @error('oe_adicao')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    @php $val = (string) old('validade_dias') @endphp
                                    <select class="form-select @error('validade_dias') is-invalid @enderror"
                                        name="validade_dias">
                                        <option value="">Selecione</option>
                                        <option value="365" {{ $val === '365' ? 'selected' : '' }}>1 Ano</option>
                                        <option value="180" {{ $val === '180' ? 'selected' : '' }}>6 Meses</option>
                                        <option value="90" {{ $val === '90' ? 'selected' : '' }}>3 Meses</option>
                                        <option value="30" {{ $val === '30' ? 'selected' : '' }}>30 Dias</option>
                                    </select>
                                    @error('validade_dias')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Diagnóstico</label>
                        <input type="text" class="form-control @error('diagnostico') is-invalid @enderror"
                            name="diagnostico" value="{{ old('diagnostico') }}">
                        @error('diagnostico')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Recomendações</label>
                        <textarea class="form-control @error('recomendacoes') is-invalid @enderror" name="recomendacoes" rows="2">{{ old('recomendacoes') }}</textarea>
                        @error('recomendacoes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Observações da Receita</label>
                        <textarea class="form-control @error('observacoes_receita') is-invalid @enderror" name="observacoes_receita"
                            rows="2">{{ old('observacoes_receita') }}</textarea>
                        @error('observacoes_receita')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save me-2"></i>
                        Salvar Receita
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
