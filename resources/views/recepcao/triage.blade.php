@extends('layouts.app')

@section('title', 'Nova Triagem')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-clipboard-text me-2"></i>
                Nova Triagem
            </h2>
            <p class="text-muted mb-0">Cadastre um novo paciente na fila de atendimento</p>
        </div>
        <a href="{{ route('recepcao.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    @if (session('validation_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('validation_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if (session('validation_warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert me-2"></i>
            {!! session('validation_warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if (session('validation_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('validation_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

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
                            <input type="text" class="form-control" id="searchPatient"
                                value="{{ old('search_term', '') }}"
                                placeholder="Digite o nome, CPF ou telefone do paciente">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="searchPatient(event)">
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

            <!-- Patient Search Results -->
            <div class="card mb-4" id="searchResults" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="mdi mdi-account-group me-2"></i>
                        Resultados da Busca
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Foram encontrados múltiplos pacientes. Selecione o paciente desejado:</p>
                    <div id="patientsList" class="list-group">
                        <!-- Lista será preenchida dinamicamente -->
                    </div>
                </div>
            </div>

            <!-- Patient Form -->
            <form id="triageForm" action="{{ route('recepcao.triage.store') }}" method="POST">
                @csrf
                <div class="card mb-4" id="patientForm"
                    style="display: {{ $errors->any() || old('nome') || old('cpf') || old('profissional_id') || old('tipo') || old('prioridade') ? 'block' : 'none' }};">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-account me-2"></i>
                            Dados do Paciente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                    name="nome" value="{{ old('nome') }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf"
                                    value="{{ old('cpf') }}" placeholder="000.000.000-00" maxlength="14">
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('nascimento_em') is-invalid @enderror"
                                    name="nascimento_em" value="{{ old('nascimento_em') }}" required>
                                @error('nascimento_em')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                    name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Triage Info -->
                <div class="card" id="triageInfo"
                    style="display: {{ $errors->any() || old('nome') || old('cpf') || old('profissional_id') || old('tipo') || old('prioridade') ? 'block' : 'none' }};">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-clipboard-pulse me-2"></i>
                            Informações da Triagem
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipo de Atendimento <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo') is-invalid @enderror" name="tipo" required>
                                    <option value="">Selecione...</option>
                                    <option value="{{ \App\Models\Consulta::TIPO_CONSULTA }}"
                                        {{ old('tipo') == \App\Models\Consulta::TIPO_CONSULTA ? 'selected' : '' }}>Consulta
                                    </option>
                                    <option value="{{ \App\Models\Consulta::TIPO_RETORNO }}"
                                        {{ old('tipo') == \App\Models\Consulta::TIPO_RETORNO ? 'selected' : '' }}>Retorno
                                    </option>
                                    <option value="{{ \App\Models\Consulta::TIPO_CONFERENCIA }}"
                                        {{ old('tipo') == \App\Models\Consulta::TIPO_CONFERENCIA ? 'selected' : '' }}>
                                        Conferência</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select @error('prioridade') is-invalid @enderror" name="prioridade"
                                    required>
                                    <option value="">Selecione...</option>
                                    <option value="{{ \App\Models\Consulta::PRIORIDADE_NORMAL }}"
                                        {{ old('prioridade') == \App\Models\Consulta::PRIORIDADE_NORMAL ? 'selected' : '' }}>
                                        Normal</option>
                                    <option value="{{ \App\Models\Consulta::PRIORIDADE }}"
                                        {{ old('prioridade') == \App\Models\Consulta::PRIORIDADE ? 'selected' : '' }}>
                                        Prioridade</option>
                                    <option value="{{ \App\Models\Consulta::PRIORIDADE_EMERGENCIA }}"
                                        {{ old('prioridade') == \App\Models\Consulta::PRIORIDADE_EMERGENCIA ? 'selected' : '' }}>
                                        Emergência</option>
                                </select>
                                @error('prioridade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profissional</label>
                                <select class="form-select @error('profissional_id') is-invalid @enderror"
                                    name="profissional_id">
                                    <option value="">Selecione...</option>
                                    @foreach ($profissionais as $profissional)
                                        <option value="{{ $profissional->id }}"
                                            {{ old('profissional_id') == $profissional->id ? 'selected' : '' }}>
                                            {{ $profissional->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('profissional_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" name="observacoes" rows="3"
                                    placeholder="Observações adicionais sobre o atendimento...">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#cancelTriageModal">
                                <i class="mdi mdi-close-circle me-2"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check-circle me-2"></i>
                                Confirmar e Adicionar à Fila
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div class="modal fade" id="cancelTriageModal" tabindex="-1" aria-labelledby="cancelTriageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pb-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                            style="width: 80px; height: 80px;">
                            <i class="mdi mdi-alert-circle text-danger" style="font-size: 48px;"></i>
                        </div>
                    </div>
                    <h5 class="modal-title mb-3" id="cancelTriageModalLabel">Cancelar Triagem</h5>
                    <p class="text-muted mb-0">
                        Tem certeza que deseja cancelar a triagem?
                    </p>
                    <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;">
                        Os dados inseridos serão perdidos e não poderão ser recuperados.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-2"></i>
                        Não Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancelTriage()">
                        <i class="mdi mdi-close-circle me-2"></i>
                        Sim, Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>
            window.searchUrl = '{{ route('recepcao.patients.search') }}';
            window.indexUrl = '{{ route('recepcao.index') }}';
            window.hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            window.hasOldData =
                {{ old('nome') || old('cpf') || old('profissional_id') || old('tipo') || old('prioridade') ? 'true' : 'false' }};
            window.hasOldName = {{ old('nome') ? 'true' : 'false' }};

            $(document).ready(function() {
                // Apply masks
                $('input[name="cpf"]').mask('000.000.000-00');
                $('input[name="telefone"]').mask('(00) 00000-0000');

                if (window.hasErrors || window.hasOldData) {
                    showFormWithOldData();
                }
            });

            function searchPatient(event) {
                if (event) {
                    event.preventDefault();
                }

                const searchTerm = document.getElementById('searchPatient').value.trim();

                if (!searchTerm) {
                    alert('Por favor, digite algo para buscar.');
                    return;
                }

                // Encontrar o botão (pode ser clicado no ícone ou no botão)
                const button = event && event.target ? (event.target.tagName === 'BUTTON' ? event.target : event.target.closest(
                    'button')) : document.querySelector('button[onclick*="searchPatient"]');
                const originalText = button ? button.innerHTML : '';

                if (button) {
                    button.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Buscando...';
                    button.disabled = true;
                }

                const url = `${window.searchUrl}?term=${encodeURIComponent(searchTerm)}`;
                console.log('🔍 Buscando paciente...');
                console.log('URL:', url);
                console.log('Termo:', searchTerm);
                console.log('searchUrl configurado:', window.searchUrl);

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('📡 Resposta recebida:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('📦 Dados recebidos:', JSON.stringify(data, null, 2));
                        console.log('Found:', data.found);
                        console.log('Multiple:', data.multiple);
                        console.log('Count:', data.count);
                        console.log('Pacientes:', data.pacientes);
                        console.log('Paciente:', data.paciente);

                        if (data.found) {
                            if (data.multiple) {
                                // Múltiplos resultados - mostrar lista
                                console.log('✓ Múltiplos pacientes encontrados');
                                showPatientsList(data.pacientes);
                                showNotification(`${data.count} pacientes encontrados. Selecione o paciente desejado.`,
                                    'info');
                            } else {
                                // Um único resultado - preencher diretamente
                                // Usar data.paciente se disponível, senão usar data.pacientes[0]
                                const paciente = data.paciente || (data.pacientes && data.pacientes[0]);
                                console.log('✓ Um único paciente encontrado:', paciente);
                                if (paciente) {
                                    fillPatientForm(paciente);
                                    showNotification('Paciente encontrado!', 'success');
                                } else {
                                    console.error('❌ Paciente não encontrado nos dados');
                                    hideAllForms();
                                    showNotification('Erro ao processar dados do paciente.', 'error');
                                }
                            }
                        } else {
                            console.log('✗ Nenhum paciente encontrado');
                            hideAllForms();
                            showNotification('Paciente não encontrado. Clique em "Novo Paciente" para cadastrar.', 'info');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erro na busca:', error);
                        console.error('Stack:', error.stack);
                        hideAllForms();
                        showNotification('Erro ao buscar paciente: ' + error.message, 'error');
                    })
                    .finally(() => {
                        if (button) {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    });
            }

            function showNewPatientForm() {
                document.getElementById('searchResults').style.display = 'none';
                const patientForm = document.getElementById('triageForm');
                patientForm.reset();
                patientForm.querySelectorAll('input').forEach(input => {
                    input.readOnly = false;
                    input.classList.remove('bg-light');
                });
                document.getElementById('searchPatient').value = '';
                document.getElementById('patientForm').style.display = 'block';
                document.getElementById('triageInfo').style.display = 'block';
                document.getElementById('patientForm').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            function hideAllForms() {
                document.getElementById('searchResults').style.display = 'none';
                document.getElementById('patientForm').style.display = 'none';
                document.getElementById('triageInfo').style.display = 'none';
            }

            function showPatientsList(pacientes) {
                hideAllForms();
                const searchResults = document.getElementById('searchResults');
                const patientsList = document.getElementById('patientsList');
                patientsList.innerHTML = '';

                pacientes.forEach(function(paciente) {
                    const listItem = document.createElement('a');
                    listItem.href = '#';
                    listItem.className = 'list-group-item list-group-item-action';
                    listItem.onclick = function(e) {
                        e.preventDefault();
                        selectPatient(paciente);
                    };

                    const idade = paciente.idade ? `(${paciente.idade} anos)` : '';
                    const telefone = paciente.telefone_formatado ? `Tel: ${paciente.telefone_formatado}` : '';
                    const cpf = paciente.cpf_formatado ? `CPF: ${paciente.cpf_formatado}` : '';

                    listItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${paciente.nome} ${idade}</h6>
                        <p class="mb-1 text-muted small">${cpf}</p>
                        ${telefone ? `<small class="text-muted">${telefone}</small>` : ''}
                    </div>
                    <small class="text-primary">
                        <i class="mdi mdi-chevron-right"></i>
                    </small>
                </div>
            `;

                    patientsList.appendChild(listItem);
                });

                searchResults.style.display = 'block';
                searchResults.scrollIntoView({
                    behavior: 'smooth'
                });
            }

            function selectPatient(paciente) {
                document.getElementById('searchResults').style.display = 'none';
                fillPatientForm(paciente);
                showNotification('Paciente selecionado!', 'success');
            }

            function fillPatientForm(paciente) {
                // Mostrar os formulários
                document.getElementById('patientForm').style.display = 'block';
                document.getElementById('triageInfo').style.display = 'block';

                const form = document.getElementById('triageForm');

                // Preencher campos do formulário
                form.querySelector('[name="nome"]').value = paciente.nome || '';

                // Usar CPF formatado se disponível, senão usar o valor bruto
                const cpfValue = paciente.cpf_formatado || paciente.cpf || '';
                form.querySelector('[name="cpf"]').value = cpfValue;

                // Converter data do formato ISO para YYYY-MM-DD
                let nascimento = '';
                if (paciente.nascimento_em) {
                    // Se já está no formato YYYY-MM-DD, usar diretamente
                    if (paciente.nascimento_em.length === 10 && paciente.nascimento_em.includes('-')) {
                        nascimento = paciente.nascimento_em;
                    } else {
                        // Extrair apenas a parte da data (YYYY-MM-DD) do formato ISO
                        nascimento = paciente.nascimento_em.split('T')[0];
                    }
                }
                form.querySelector('[name="nascimento_em"]').value = nascimento;

                // Usar telefone formatado se disponível, senão usar o valor bruto
                const telefoneValue = paciente.telefone_formatado || paciente.telefone || '';
                form.querySelector('[name="telefone"]').value = telefoneValue;

                form.querySelector('[name="email"]').value = paciente.email || '';

                // Marcar campos como readonly para pacientes existentes
                form.querySelectorAll('input').forEach(input => {
                    input.readOnly = true;
                    input.classList.add('bg-light');
                });

                // Reaplicar máscaras nos campos formatados
                if (typeof $ !== 'undefined' && $.fn.mask) {
                    $('input[name="cpf"]').mask('000.000.000-00');
                    $('input[name="telefone"]').mask('(00) 00000-0000');
                }

                // Scroll para o formulário
                document.getElementById('patientForm').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            function showFormWithOldData() {
                const patientForm = document.getElementById('patientForm');
                const triageInfo = document.getElementById('triageInfo');

                if (patientForm) {
                    patientForm.style.display = 'block';
                    patientForm.style.visibility = 'visible';
                }

                if (triageInfo) {
                    triageInfo.style.display = 'block';
                    triageInfo.style.visibility = 'visible';
                }

                setTimeout(function() {
                    if (patientForm) {
                        patientForm.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);

                if (window.hasOldName && !window.hasErrors) {
                    const form = document.getElementById('triageForm');
                    if (form) {
                        form.querySelectorAll(
                            'input[name="nome"], input[name="cpf"], input[name="nascimento_em"], input[name="telefone"], input[name="email"]'
                            ).forEach(input => {
                            input.readOnly = true;
                            input.classList.add('bg-light');
                        });
                    }
                }
            }

            function confirmCancelTriage() {
                // Fechar o modal
                const modalElement = document.getElementById('cancelTriageModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }

                // Redirecionar para a página de recepção
                window.location.href = window.indexUrl;
            }

            function showNotification(message, type) {
                const colors = {
                    success: '#28a745',
                    error: '#dc3545',
                    info: '#17a2b8'
                };

                const notification = document.createElement('div');
                notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 10000);
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('searchPatient').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchPatient(e);
                    }
                });

                document.getElementById('triageForm').addEventListener('submit', function(e) {
                    const form = e.target;
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        form.reportValidity();
                        return false;
                    }

                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        const originalText = submitButton.innerHTML;
                        submitButton.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Salvando...';
                        submitButton.disabled = true;

                        setTimeout(() => {
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        }, 5000);
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .form-label {
                font-weight: 600;
                color: #495057;
            }

            .list-group-item-action:hover {
                background-color: rgba(0, 123, 255, 0.05);
                border-color: rgba(0, 123, 255, 0.25);
            }

            #searchResults .list-group-item {
                transition: all 0.2s ease-in-out;
            }

            #searchResults .list-group-item:hover {
                transform: translateX(5px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .form-control.is-invalid {
                border-color: #dc3545;
            }
        </style>
    @endpush
@endsection
