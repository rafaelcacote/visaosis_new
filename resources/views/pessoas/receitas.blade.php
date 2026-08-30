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
                Cliente
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
            <form method="POST" enctype="multipart/form-data"
                action="{{ $isEditing ? route('pessoas.receitas.update', ['pessoa' => $pessoa->id, 'prescricao' => $prescricaoForm->id]) : route('pessoas.receitas.store', $pessoa->id) }}">
                @csrf
                @if ($isEditing)
                    @method('PATCH')
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Nome do profissional que prescreveu</label>
                        <input type="text" class="form-control @error('especialista_externo') is-invalid @enderror"
                            name="especialista_externo"
                            value="{{ old('especialista_externo', $isEditing ? $prescricaoForm->especialista_externo : null) }}"
                            placeholder="Ex: Dr(a). Fulano de Tal">
                        @error('especialista_externo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data da Receita</label>
                        <input type="date" class="form-control @error('data_receita') is-invalid @enderror"
                            name="data_receita"
                            value="{{ old('data_receita', $isEditing ? optional($prescricaoForm->data_receita)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                        @error('data_receita')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @php
                    $rxFotoAtualUrl = $isEditing && $prescricaoForm ? $prescricaoForm->receita_foto_url ?? null : null;
                @endphp
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Foto / Anexo da Receita</label>
                        <div class="input-group flex-wrap">
                            <label class="btn btn-outline-secondary" role="button"
                                title="Abrir câmera traseira no celular">
                                <i class="mdi mdi-camera-outline me-1"></i>Tirar Foto
                                <input type="file" accept="image/*" capture="environment" class="d-none"
                                    id="rxPhotoCamera" onchange="window.copyRxCameraToMain(event)">
                            </label>
                            <label class="btn btn-outline-info" role="button" title="Anexar imagem existente">
                                <i class="mdi mdi-folder-multiple-image me-1"></i>Anexar Imagem
                                <input type="file" accept="image/*" class="d-none" name="receita_foto" id="rxPhotoFile"
                                    onchange="window.handleRxPhotoSelect(event)">
                            </label>
                            <button class="btn btn-outline-danger" type="button" id="rxPhotoClearBtn"
                                onclick="window.clearRxPhoto()" style="display:none;" title="Remover foto selecionada">
                                <i class="mdi mdi-delete-outline"></i> Limpar
                            </button>
                            @if ($isEditing && $prescricaoForm && $prescricaoForm->receita_foto_caminho)
                                <label class="btn btn-outline-warning ms-auto" role="button"
                                    title="Marcar para remover a foto atual ao salvar">
                                    <input type="checkbox" name="remover_receita_foto" id="rxRemovePhotoCb" value="1"
                                        class="form-check-input me-1 d-none" onchange="window.toggleRxRemovePhoto(this)">
                                    <i class="mdi mdi-camera-off-outline me-1"></i>
                                    <span id="rxRemovePhotoText">Remover Foto Atual</span>
                                </label>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-1">
                            Formatos aceitos: PNG, JPG, JPEG, WEBP. Tamanho máximo: 10MB.
                            No celular, "Tirar Foto" abre a câmera traseira automaticamente.
                        </small>
                        @error('receita_foto')
                            <div class="text-danger small mt-1 d-block">{{ $message }}</div>
                        @enderror

                        <div id="rxPhotoPreviewWrap" class="mt-2" style="display:none;">
                            <div class="d-flex gap-3 align-items-start flex-wrap">
                                <div class="position-relative">
                                    <img id="rxPhotoPreview" class="img-thumbnail border rounded"
                                        style="max-height:180px; max-width:100%; object-fit:cover;" alt="Preview">
                                </div>
                                <div class="small">
                                    <div class="text-muted mb-1">Arquivo selecionado</div>
                                    <div id="rxPhotoFileName">-</div>
                                    <div id="rxPhotoFileSize" class="text-muted">-</div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                        onclick="window.openRxPhotoPreviewLightbox()" id="rxPhotoPreviewZoomBtn" disabled>
                                        <i class="mdi mdi-magnify-plus-outline me-1"></i>Ampliar
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if ($rxFotoAtualUrl)
                            <div id="rxFotoAtualWrap" class="mt-3">
                                <div class="card border-warning shadow-sm bg-warning bg-opacity-5">
                                    <div class="card-body p-2 d-flex align-items-start gap-3 flex-wrap">
                                        <div class="position-relative">
                                            <img id="rxFotoAtualImg" src="{{ $rxFotoAtualUrl }}"
                                                class="img-thumbnail border-0 rounded"
                                                style="max-height:140px; object-fit:cover;"
                                                onclick="window.openRxPhotoLightbox('{{ $rxFotoAtualUrl }}')"
                                                role="button" alt="Foto atual da receita">
                                        </div>
                                        <div class="small flex-grow-1 min-w-0">
                                            <div class="fw-semibold mb-1">
                                                <i class="mdi mdi-image-outline me-1 text-warning"></i>
                                                Foto Atual da Receita
                                            </div>
                                            <div class="text-muted mb-2">
                                                Imagem anexada no cadastro desta receita. Clique na miniatura para ampliar.
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="window.openRxPhotoLightbox('{{ $rxFotoAtualUrl }}')">
                                                    <i class="mdi mdi-magnify-plus-outline me-1"></i>Ampliar
                                                </button>
                                                <a href="{{ $rxFotoAtualUrl }}" target="_blank" rel="noopener" download
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="mdi mdi-download-outline me-1"></i>Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @include('components.prescricao.form-fields', [
                    'isEditing' => $isEditing,
                    'prescricaoForm' => $prescricaoForm,
                    'validadeOptions' => [
                        '365' => '1 Ano',
                        '180' => '6 Meses',
                    ],
                ])

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
                <div class="text-muted">Nenhuma receita cadastrada para este cliente.</div>
            @else
                @php
                    $now = \Carbon\Carbon::now();

                    $historyItems = $prescricoes->values()->map(function ($prescricao) use ($pessoa, $now) {
                        $dataReceita = $prescricao->data_receita
                            ? \Carbon\Carbon::parse($prescricao->data_receita)->startOfDay()
                            : ($prescricao->created_at
                                ? $prescricao->created_at->copy()
                                : null);
                        $validadeDias = $prescricao->validade_dias ? (int) $prescricao->validade_dias : null;
                        $validUntil =
                            $dataReceita && $validadeDias ? $dataReceita->copy()->addDays($validadeDias) : null;

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
                            'data' => $dataReceita ? $dataReceita->format('d/m/Y') : '-',
                            'data_hora' => $dataReceita ? $dataReceita->format('d/m/Y') : '-',
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
                            'foto_url' => $prescricao->receita_foto_url,
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

                    <div id="rxFotoHistoricoWrap" class="mb-3" style="display:none;">
                        <div class="card border-primary bg-opacity-5 bg-primary shadow-sm">
                            <div class="card-body p-2 d-flex align-items-start gap-3 flex-wrap">
                                <div class="position-relative">
                                    <img id="rxFotoHistoricoImg" src="" class="img-thumbnail border-0 rounded"
                                        style="max-height:150px; cursor:pointer; object-fit:cover;"
                                        onclick="window.openRxPhotoLightbox(document.getElementById('rxFotoHistoricoImg').src)"
                                        alt="Foto da receita">
                                </div>
                                <div class="small flex-grow-1 min-w-0">
                                    <div class="fw-semibold mb-1">
                                        <i class="mdi mdi-image-outline me-1 text-primary"></i>
                                        Foto da Receita
                                    </div>
                                    <div class="text-muted mb-2">
                                        Imagem anexada a esta receita. Clique na miniatura para ampliar.
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            id="rxFotoHistoricoZoomBtn"
                                            onclick="window.openRxPhotoLightbox(document.getElementById('rxFotoHistoricoImg').src)">
                                            <i class="mdi mdi-magnify-plus-outline me-1"></i>Ampliar
                                        </button>
                                        <a href="#" id="rxFotoHistoricoDownload" target="_blank" rel="noopener"
                                            download class="btn btn-sm btn-outline-secondary">
                                            <i class="mdi mdi-download-outline me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                                    <th class="text-center">Adição</th>
                                    <th class="text-center">DNP</th>
                                    <th class="text-center">Altura</th>
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
                                    <td class="text-center" id="rxLongeOdAdicao">-</td>
                                    <td class="text-center" id="rxLongeOdDnp">-</td>
                                    <td class="text-center" id="rxLongeOdAltura">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OE
                                    </td>
                                    <td class="text-center" id="rxLongeOeEsferico">-</td>
                                    <td class="text-center" id="rxLongeOeCilindrico">-</td>
                                    <td class="text-center" id="rxLongeOeEixo">-</td>
                                    <td class="text-center" id="rxLongeOeAv">-</td>
                                    <td class="text-center" id="rxLongeOeAdicao">-</td>
                                    <td class="text-center" id="rxLongeOeDnp">-</td>
                                    <td class="text-center" id="rxLongeOeAltura">-</td>
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
                                    <td class="text-center" id="rxPertoOdAdicao">-</td>
                                    <td class="text-center" id="rxPertoOdDnp">-</td>
                                    <td class="text-center" id="rxPertoOdAltura">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                                        <i class="mdi mdi-eye-outline me-1"></i>OE
                                    </td>
                                    <td class="text-center" id="rxPertoOeEsferico">-</td>
                                    <td class="text-center" id="rxPertoOeCilindrico">-</td>
                                    <td class="text-center" id="rxPertoOeEixo">-</td>
                                    <td class="text-center" id="rxPertoOeAv">-</td>
                                    <td class="text-center" id="rxPertoOeAdicao">-</td>
                                    <td class="text-center" id="rxPertoOeDnp">-</td>
                                    <td class="text-center" id="rxPertoOeAltura">-</td>
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

    <div class="modal fade" id="rxPhotoLightboxModal" tabindex="-1" aria-labelledby="rxPhotoLightboxLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h5 class="modal-title fs-6" id="rxPhotoLightboxLabel">
                        <i class="mdi mdi-image-outline me-1"></i>
                        Foto da Receita
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" id="rxPhotoLightboxDownload" target="_blank" rel="noopener" download
                            class="btn btn-sm btn-outline-secondary">
                            <i class="mdi mdi-download-outline me-1"></i>Download
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                </div>
                <div class="modal-body p-2 bg-light">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
                        <img id="rxPhotoLightboxImg" src="" alt="Foto ampliada"
                            class="img-fluid rounded shadow-sm">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
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

                const fotoHistoricoWrap = document.getElementById('rxFotoHistoricoWrap');
                const fotoHistoricoImg = document.getElementById('rxFotoHistoricoImg');
                const fotoHistoricoDownload = document.getElementById('rxFotoHistoricoDownload');
                const fotoUrl = item.foto_url || null;
                if (fotoHistoricoWrap && fotoHistoricoImg && fotoHistoricoDownload) {
                    if (fotoUrl) {
                        fotoHistoricoImg.src = fotoUrl;
                        fotoHistoricoDownload.href = fotoUrl;
                        fotoHistoricoWrap.style.display = '';
                    } else {
                        fotoHistoricoImg.removeAttribute('src');
                        fotoHistoricoDownload.href = '#';
                        fotoHistoricoWrap.style.display = 'none';
                    }
                }

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

        (function() {
            const form = document.querySelector('form[action*="receitas"]');
            if (!form) return;

            const getField = (name) => form.querySelector(`[name="${name}"]`);

            const parseRxNumber = (value) => {
                if (value == null) return null;
                const s = String(value).trim().replace(',', '.');
                if (s === '') return null;
                const normalized = s.startsWith('+') ? s.slice(1) : s;
                const n = Number(normalized);
                return Number.isFinite(n) ? n : null;
            };

            const parseSphereNumber = (value) => {
                const s = String(value ?? '').trim().toUpperCase();
                if (s === 'PL') return 0;
                return parseRxNumber(value);
            };

            const formatRxNumber = (n) => {
                if (!Number.isFinite(n)) return '';
                const fixed = n.toFixed(2);
                return n > 0 ? `+${fixed}` : fixed;
            };

            const manualFields = [
                'od_esferico_perto',
                'oe_esferico_perto',
                'od_cilindrico_perto',
                'oe_cilindrico_perto',
                'od_eixo_perto',
                'oe_eixo_perto',
            ];

            manualFields.forEach((name) => {
                const input = getField(name);
                if (!input) return;
                input.addEventListener('input', () => {
                    const v = String(input.value ?? '').trim();
                    if (v === '') {
                        delete input.dataset.manual;
                        return;
                    }
                    input.dataset.manual = '1';
                });
            });

            const setIfNotManual = (name, value) => {
                const input = getField(name);
                if (!input) return;
                if (input.dataset.manual === '1') return;
                input.value = value;
            };

            const updateNearFromAddition = () => {
                const odAdd = parseRxNumber(getField('od_adicao')?.value);
                const oeAddRaw = parseRxNumber(getField('oe_adicao')?.value);
                const oeAdd = oeAddRaw != null ? oeAddRaw : odAdd;

                const odEsf = parseSphereNumber(getField('od_esferico')?.value);
                const oeEsf = parseSphereNumber(getField('oe_esferico')?.value);

                if (odAdd != null && odEsf != null) {
                    setIfNotManual('od_esferico_perto', formatRxNumber(odEsf + odAdd));
                    setIfNotManual('od_cilindrico_perto', String(getField('od_cilindrico')?.value ?? ''));
                    setIfNotManual('od_eixo_perto', String(getField('od_eixo')?.value ?? ''));
                }

                if (oeAdd != null && oeEsf != null) {
                    setIfNotManual('oe_esferico_perto', formatRxNumber(oeEsf + oeAdd));
                    setIfNotManual('oe_cilindrico_perto', String(getField('oe_cilindrico')?.value ?? ''));
                    setIfNotManual('oe_eixo_perto', String(getField('oe_eixo')?.value ?? ''));
                }
            };

            [
                'od_adicao',
                'oe_adicao',
                'od_esferico',
                'oe_esferico',
                'od_cilindrico',
                'oe_cilindrico',
                'od_eixo',
                'oe_eixo',
            ].forEach((name) => {
                const input = getField(name);
                if (!input) return;
                input.addEventListener('input', updateNearFromAddition);
            });

            updateNearFromAddition();
        })();

        (function initReceitaFotoWidgets() {
            const mainInput = document.getElementById('rxPhotoFile');
            const cameraInput = document.getElementById('rxPhotoCamera');
            const previewWrap = document.getElementById('rxPhotoPreviewWrap');
            const previewImg = document.getElementById('rxPhotoPreview');
            const fileNameEl = document.getElementById('rxPhotoFileName');
            const fileSizeEl = document.getElementById('rxPhotoFileSize');
            const clearBtn = document.getElementById('rxPhotoClearBtn');
            const previewZoomBtn = document.getElementById('rxPhotoPreviewZoomBtn');
            const removeCb = document.getElementById('rxRemovePhotoCb');
            const removeText = document.getElementById('rxRemovePhotoText');
            const atualWrap = document.getElementById('rxFotoAtualWrap');

            let currentPreviewUrl = null;

            const formatBytes = (bytes) => {
                if (bytes == null || isNaN(bytes)) return '-';
                const b = Number(bytes);
                if (b < 1024) return b + ' B';
                if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
                return (b / (1024 * 1024)).toFixed(2) + ' MB';
            };

            const revokeCurrentPreview = () => {
                if (currentPreviewUrl) {
                    try {
                        URL.revokeObjectURL(currentPreviewUrl);
                    } catch (e) {}
                    currentPreviewUrl = null;
                }
            };

            const setFileFromFileList = (fileList) => {
                if (!mainInput || !fileList || !fileList.length) return;
                try {
                    mainInput.files = fileList;
                } catch (e) {
                    const dt = new DataTransfer();
                    Array.from(fileList).forEach(f => dt.items.add(f));
                    mainInput.files = dt.files;
                }
                const ev = new Event('change', {
                    bubbles: true
                });
                mainInput.dispatchEvent(ev);
            };

            window.copyRxCameraToMain = (event) => {
                const input = event && event.target ? event.target : cameraInput;
                if (!input || !mainInput) return;
                if (input.files && input.files.length) {
                    setFileFromFileList(input.files);
                }
                input.value = '';
            };

            window.handleRxPhotoSelect = (event) => {
                const input = (event && event.target) ? event.target : mainInput;
                if (!input || !previewImg) return;
                const file = input.files && input.files.length ? input.files[0] : null;
                if (!file) {
                    window.clearRxPhoto(true);
                    return;
                }

                revokeCurrentPreview();
                currentPreviewUrl = URL.createObjectURL(file);
                previewImg.src = currentPreviewUrl;
                if (fileNameEl) fileNameEl.textContent = file.name || 'imagem';
                if (fileSizeEl) fileSizeEl.textContent = formatBytes(file.size);
                if (previewWrap) previewWrap.style.display = '';
                if (clearBtn) clearBtn.style.display = '';
                if (previewZoomBtn) previewZoomBtn.disabled = false;
                if (removeCb) {
                    removeCb.checked = false;
                    window.toggleRxRemovePhoto(removeCb);
                }
            };

            window.clearRxPhoto = (keepFileInputs) => {
                revokeCurrentPreview();
                if (mainInput) {
                    try {
                        mainInput.value = '';
                    } catch (e) {}
                }
                if (cameraInput) {
                    try {
                        cameraInput.value = '';
                    } catch (e) {}
                }
                if (previewWrap) previewWrap.style.display = 'none';
                if (previewImg) previewImg.removeAttribute('src');
                if (fileNameEl) fileNameEl.textContent = '-';
                if (fileSizeEl) fileSizeEl.textContent = '-';
                if (clearBtn) clearBtn.style.display = 'none';
                if (previewZoomBtn) previewZoomBtn.disabled = true;
            };

            window.toggleRxRemovePhoto = (cbEl) => {
                if (!cbEl || !removeText || !atualWrap) return;
                const checked = !!cbEl.checked;
                if (checked) {
                    atualWrap.classList.add('opacity-50');
                    atualWrap.style.opacity = '0.5';
                    removeText.textContent = 'Foto será removida';
                } else {
                    atualWrap.classList.remove('opacity-50');
                    atualWrap.style.opacity = '';
                    removeText.textContent = 'Remover Foto Atual';
                }
            };

            let lightboxInstance = null;
            window.openRxPhotoLightbox = (url) => {
                if (!url) return;
                const modal = document.getElementById('rxPhotoLightboxModal');
                const img = document.getElementById('rxPhotoLightboxImg');
                const dl = document.getElementById('rxPhotoLightboxDownload');
                if (!modal || !img || !dl) return;
                img.src = url;
                dl.href = url;
                try {
                    if (window.bootstrap && window.bootstrap.Modal) {
                        if (!lightboxInstance) lightboxInstance = new window.bootstrap.Modal(modal, {
                            backdrop: true,
                            keyboard: true
                        });
                        lightboxInstance.show();
                    } else if (window.jQuery) {
                        window.jQuery(modal).modal({
                            backdrop: true,
                            keyboard: true
                        });
                        window.jQuery(modal).modal('show');
                    } else {
                        modal.classList.add('show');
                        modal.style.display = 'block';
                        document.body.classList.add('modal-open');
                    }
                } catch (e) {
                    window.open(url, 'rx_photo_lightbox', 'noopener');
                }
            };

            window.openRxPhotoPreviewLightbox = () => {
                if (currentPreviewUrl) {
                    window.openRxPhotoLightbox(currentPreviewUrl);
                }
            };

            if (removeCb) {
                window.toggleRxRemovePhoto(removeCb);
            }
        })();
    </script>
@endpush
