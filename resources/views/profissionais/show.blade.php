@extends('layouts.app')

@section('title', $profissional->nome . ' - VisaoSis')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-tie me-2"></i>
            Profissional
        </h2>
        <p class="text-muted mb-0">Detalhes do profissional</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
        <a href="{{ route('profissionais.edit', $profissional) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil me-2"></i>
            Editar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Dados Principais -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="mdi mdi-account-card-details me-2"></i>
                    Dados do Profissional
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label text-muted">Nome</label>
                        <div class="fw-medium">{{ $profissional->nome }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">CPF</label>
                        <div class="fw-medium">{{ $profissional->cpf_formatado ?: 'Não informado' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @if ($profissional->ativo)
                                <span class="badge bg-success fs-6">
                                    <i class="mdi mdi-check-circle me-1"></i>
                                    Ativo
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">
                                    <i class="mdi mdi-pause-circle me-1"></i>
                                    Inativo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Especialidade</label>
                        <div class="fw-medium">{{ $profissional->especialidade?->descricao ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Registro</label>
                        <div class="fw-medium">{{ $profissional->registro_conselho ?? '-' }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Sexo</label>
                        <div class="fw-medium">{{ $profissional->sexo_texto }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-muted">Nascimento</label>
                        <div class="fw-medium">{{ $profissional->nascimento_em?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-muted">Idade</label>
                        <div class="fw-medium">{{ $profissional->idade ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informações de Contato -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="mdi mdi-phone me-2"></i>
                    Contato
                </h5>
            </div>
            <div class="card-body">
                @if ($profissional->telefone)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Telefone</label>
                        <div class="fw-medium">
                            {{ $profissional->telefone_formatado }}
                        </div>
                    </div>
                @endif

                @if ($profissional->email)
                    <div class="mb-3">
                        <label class="form-label text-muted small">E-mail</label>
                        <div class="fw-medium">
                            {{ $profissional->email }}
                        </div>
                    </div>
                @endif

                @if ($profissional->chave_pix)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Chave PIX</label>
                        <div class="fw-medium font-monospace small d-flex align-items-center">
                            <span class="me-2">{{ $profissional->chave_pix }}</span>
                            <i class="mdi mdi-content-copy text-primary fs-5 cursor-pointer" onclick="copiarChavePix()"
                                title="Copiar chave PIX" style="cursor: pointer;"></i>
                        </div>
                    </div>
                @endif

                @if (!$profissional->telefone && !$profissional->email && !$profissional->chave_pix)
                    <div class="text-center text-muted py-3">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Nenhuma informação de contato cadastrada
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card mt-3">
            <div class="card-header bg-secondary">
                <h5 class="mb-0">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Informações do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label text-muted small">Cadastrado em</label>
                    <div class="small">{{ $profissional->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if ($profissional->updated_at && $profissional->updated_at != $profissional->created_at)
                    <div class="mb-2">
                        <label class="form-label text-muted small">Última atualização</label>
                        <div class="small">{{ $profissional->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .card-header.bg-secondary {
        background-color: #e9ecef !important;
        border-color: #e9ecef !important;
        color: black !important;
    }
</style>
@endsection

@push('scripts')
<script>
    function copiarChavePix() {
        const chavePix = "{{ $profissional->chave_pix }}";
        navigator.clipboard.writeText(chavePix).then(function() {
            const icon = event.target;
            const originalClass = icon.className;

            icon.className = 'mdi mdi-check-circle text-success fs-5 cursor-pointer';
            icon.style.transform = 'scale(1.1)';

            setTimeout(() => {
                icon.className = originalClass;
                icon.style.transform = '';
            }, 2000);
        });
    }
</script>
@endpush
