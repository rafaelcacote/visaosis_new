@extends('layouts.app')

@section('title', 'Nova Triagem - Atendimento')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-clipboard-text me-2"></i>
            Nova Triagem
        </h2>
        <p class="text-muted mb-0">Cadastre um novo paciente na fila de atendimento</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-exclamation-triangle me-2"></i>
        <strong>Erro de Validação:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <!-- Search Patient -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-magnify me-2"></i>
                    Buscar Paciente
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchPatient" placeholder="Digite o nome, CPF ou telefone do paciente">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="searchPatient()">
                            <i class="mdi mdi-magnify"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100" onclick="showNewPatientForm()">
                            <i class="mdi mdi-account-plus"></i> Novo Paciente
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Form -->
        <div class="card mb-4" id="patientForm" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-account me-2"></i>
                    Dados do Paciente
                </h5>
            </div>
            <div class="card-body">
                <form id="triageForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control" name="cpf" placeholder="000.000.000-00" maxlength="14">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="birth_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="phone" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Triage Info -->
        <div class="card" id="triageInfo" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="mdi mdi-clipboard-pulse me-2"></i>
                    Informações da Triagem
                </h5>
            </div>
            <div class="card-body">
                <form id="triageDetailsForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Atendimento <span class="text-danger">*</span></label>
                            <select class="form-select" name="service_type" required>
                                <option value="">Selecione...</option>
                                <option value="consulta">Consulta</option>
                                <option value="retorno">Retorno</option>
                                <option value="conferencia">Conferência</option>
                                <option value="emergencia">Emergência</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prioridade <span class="text-danger">*</span></label>
                            <select class="form-select" name="priority" required>
                                <option value="">Selecione...</option>
                                <option value="normal">Normal</option>
                                <option value="urgent">Prioridade</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profissional <span class="text-danger">*</span></label>
                            <select class="form-select" name="professional_id" required>
                                <option value="">Selecione...</option>
                                @foreach ($professionals ?? [] as $professional)
                                    <option value="{{ $professional->id ?? $professional['id'] }}">
                                        {{ $professional->name ?? $professional['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Observações adicionais sobre o atendimento..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="mdi mdi-close-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-success" onclick="saveTriage()">
                            <i class="mdi mdi-check-circle me-2"></i>
                            Confirmar e Adicionar à Fila
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function searchPatient() {
        const searchTerm = document.getElementById('searchPatient').value.trim();
        
        if (!searchTerm) {
            alert('Por favor, digite algo para buscar.');
            return;
        }

        // Implement AJAX search
        fetch(`{{ route('attendance.patients.search') }}?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    if (data.multiple && data.pacientes) {
                        // Múltiplos resultados - mostrar lista
                        showPatientsList(data.pacientes);
                    } else {
                        // Um único resultado - preencher diretamente
                        const paciente = data.paciente || (data.pacientes && data.pacientes[0]);
                        if (paciente) {
                            fillPatientForm(paciente);
                        } else {
                            showNewPatientForm();
                        }
                    }
                } else {
                    showNewPatientForm();
                }
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                showNewPatientForm();
            });
    }

    function showNewPatientForm() {
        document.getElementById('patientForm').style.display = 'block';
        document.getElementById('triageInfo').style.display = 'block';
        document.getElementById('patientForm').scrollIntoView({ behavior: 'smooth' });
    }

    function showPatientsList(pacientes) {
        // Criar modal ou área para mostrar múltiplos pacientes
        let message = 'Foram encontrados múltiplos pacientes:\n\n';
        pacientes.forEach((p, index) => {
            message += `${index + 1}. ${p.nome}\n`;
        });
        message += '\nPor favor, use a busca com mais detalhes ou cadastre um novo paciente.';
        alert(message);
        showNewPatientForm();
    }

    function fillPatientForm(patient) {
        document.getElementById('patientForm').style.display = 'block';
        document.getElementById('triageInfo').style.display = 'block';
        
        const form = document.getElementById('triageForm');
        form.querySelector('[name="name"]').value = patient.nome || '';
        form.querySelector('[name="cpf"]').value = patient.cpf_formatado || patient.cpf || '';
        
        // Converter data do formato ISO para YYYY-MM-DD
        let birthDate = '';
        if (patient.nascimento_em) {
            birthDate = patient.nascimento_em.split('T')[0];
        }
        form.querySelector('[name="birth_date"]').value = birthDate;
        
        form.querySelector('[name="phone"]').value = patient.telefone_formatado || patient.telefone || '';
        form.querySelector('[name="email"]').value = patient.email || '';
        
        document.getElementById('patientForm').scrollIntoView({ behavior: 'smooth' });
    }

    function saveTriage() {
        const patientData = new FormData(document.getElementById('triageForm'));
        const triageData = new FormData(document.getElementById('triageDetailsForm'));

        // Combine forms data
        const formData = new FormData();
        for (const [key, value] of patientData.entries()) {
            formData.append(key, value);
        }
        for (const [key, value] of triageData.entries()) {
            formData.append(key, value);
        }

        // Send to server
        fetch('{{ route('attendance.triage.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('attendance.index') }}';
            } else {
                alert('Erro ao salvar triagem: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar triagem.');
        });
    }

    // Permitir busca com Enter
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchPatient');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchPatient();
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush
