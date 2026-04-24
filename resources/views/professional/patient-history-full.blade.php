@extends('layouts.app')
@section('title', 'Histórico Completo - ' . $patient['nome'])

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-history me-2"></i>
                Histórico Completo - {{ $patient['nome'] }}
            </h2>
            <p class="text-muted mb-0">Histórico de atendimentos do paciente</p>
        </div>
        <div>
            @if (isset($returnTo) && $returnTo === 'consultation' && isset($consultaId))
                <a href="{{ route('professional.consultation', $consultaId) }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar à Consulta
                </a>
            @else
                <a href="{{ route('professional.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar à Fila
                </a>
            @endif
        </div>
    </div>
    <div class="container-fluid py-4">
        <!-- Patient Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-3">{{ $patient['nome'] }}</h4>
                        <p class="mb-1"><strong>Idade:</strong> {{ $patient['idade'] }} anos</p>
                        <p class="mb-1"><strong>CPF:</strong> {{ $patient['cpf'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Telefone:</strong> {{ $patient['telefone'] }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $patient['email'] }}</p>
                        <p class="mb-1"><strong>Última Consulta:</strong>
                            {{ date('d/m/Y', strtotime($patient['ultima_consulta'])) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Histórico de Atendimentos</h5>
            </div>
            <div class="card-body">
                <div class="timeline-wrapper">
                    @foreach ($history as $consultation)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                {{ date('d/m/Y', strtotime($consultation['data'])) }} às {{ $consultation['hora'] }}
                            </div>
                            <div class="timeline-content">
                                <div class="mb-2">
                                    <span class="badge bg-primary">{{ $consultation['tipo'] }}</span>
                                    @if ($consultation['receita'])
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">
                                                    <i class="mdi mdi-file-document text-success me-2"></i>
                                                    Receita
                                                </h6>
                                                <a href="{{ route('professional.printPrescription', $consultation['id']) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-success">
                                                    <i class="mdi mdi-printer"></i> Imprimir
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <!-- Tabela de Prescrição -->
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="80">Olho</th>
                                                                <th>Esférico</th>
                                                                <th>Cilíndrico</th>
                                                                <th>Eixo</th>
                                                                <th>AV</th>
                                                                <th>DNP</th>
                                                                <th>Altura </th>
                                                                <th>Adição</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center fw-bold">OD</td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_esferico'] }}</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_cilindrico'] }} </strong>
                                                                </td>
                                                                <td>
                                                                    <strong> {{ $consultation['od_eixo'] }}°</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_acuidade'] }}</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_dnp'] }}</strong>

                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_altura'] }}</strong>

                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['od_adicao'] }}</strong>

                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="text-center fw-bold">OE</td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_esferico'] }}</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_cilindrico'] }} </strong>
                                                                </td>
                                                                <td>
                                                                    <strong> {{ $consultation['oe_eixo'] }}°</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_acuidade'] }}</strong>
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_dnp'] }}</strong>

                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_dnp'] }}</strong>

                                                                </td>
                                                                <td>
                                                                    <strong>{{ $consultation['oe_altura'] }}</strong>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="mt-2">
                                                    <small><strong>Obs:</strong>
                                                        {{ $consultation['observacoes_receita'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if (!empty($consultation['exame']))
                                    <div class="card mt-2">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">
                                                <i class="mdi mdi-eye text-info me-2"></i>
                                                Exame
                                            </h6>
                                            <a href="{{ route('professional.print-exame', $consultation['id']) }}"
                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-printer"></i> Imprimir
                                            </a>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center mb-2">
                                                <div class="col-6"><small class="text-muted">PIO OD</small>
                                                    <div><strong>{{ $consultation['pio_od'] }}</strong></div>
                                                </div>
                                                <div class="col-6"><small class="text-muted">PIO OE</small>
                                                    <div><strong>{{ $consultation['pio_oe'] }}</strong></div>
                                                </div>
                                            </div>
                                            @if (!empty($consultation['fundoscopia']))
                                                <div class="mt-2"><small><strong>Fundoscopia:</strong>
                                                        {{ $consultation['fundoscopia'] }}</small></div>
                                            @endif
                                            @if (!empty($consultation['anamnese']))
                                                <div class="mt-2"><small><strong>Anamnese:</strong>
                                                        {{ $consultation['anamnese'] }}</small></div>
                                            @endif
                                            @if (!empty($consultation['observacoes_exame']))
                                                <div class="mt-2"><small><strong>Obs:</strong>
                                                        {{ $consultation['observacoes_exame'] }}</small></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($consultation['encaminhamento']))
                                    <div class="card mt-2">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">
                                                <i class="mdi mdi-arrow-right-circle text-primary me-2"></i>
                                                Encaminhamento
                                            </h6>
                                            <a href="{{ route('professional.print-referral', $consultation['id']) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-printer"></i> Imprimir
                                            </a>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <span
                                                    class="badge bg-secondary">{{ $consultation['esp_descricao'] }}</span>
                                                <span
                                                    class="badge bg-{{ $consultation['urgencia'] === 'emergencia' ? 'danger' : ($consultation['urgencia'] === 'urgente' ? 'warning' : 'success') }} ms-1">{{ ucfirst($consultation['urgencia']) }}</span>
                                            </div>
                                            @if (!empty($consultation['hipotese']))
                                                <div class="mt-1"><small><strong>Hipótese:</strong>
                                                        {{ $consultation['hipotese'] }}</small></div>
                                            @endif
                                            @if (!empty($consultation['ultima_avaliacao_format']))
                                                <div class="mt-1"><small><strong>Última Avaliação:</strong>
                                                        {{ $consultation['ultima_avaliacao_format'] }}</small></div>
                                            @endif
                                            @if (!empty($consultation['observacoes_encaminhamento']))
                                                <div class="mt-1"><small><strong>Obs:</strong>
                                                        {{ $consultation['observacoes_encaminhamento'] }}</small></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <p class="mt-2"><strong>Diagnóstico:</strong> {{ $consultation['diagnostico'] }}</p>
                                @if ($consultation['observacoes'])
                                    <p><strong>Observações:</strong> {{ $consultation['observacoes'] }}</p>
                                @endif
                                <div class="timeline-doctor">
                                    Atendido por {{ $consultation['profissional'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline-wrapper {
            position: relative;
            padding-left: 50px;
        }

        .timeline-item {
            position: relative;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -35px;
            top: 1.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #3b82f6;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -29px;
            top: 2rem;
            height: calc(100% + 1.5rem);
            border-left: 2px solid #e5e7eb;
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-date {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        .timeline-content {
            font-size: 0.95rem;
        }

        .timeline-doctor {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 1rem;
            font-style: italic;
        }
    </style>
@endpush
