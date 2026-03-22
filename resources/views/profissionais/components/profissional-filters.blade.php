<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body profissional-filters">
                <form method="GET" action="{{ route('profissionais.index') }}" class="row g-3 align-items-end form-aligned-sm js-list-filter-form">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nome, CPF ou email">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="especialidade_id" class="form-label">Especialidade</label>
                        <select class="form-select form-select-sm" id="especialidade_id" name="especialidade_id">
                            <option value="">Todas</option>
                            @foreach($especialidades ?? [] as $esp)
                                <option value="{{ $esp->id }}" {{ ($filters['especialidade_id'] ?? '') == $esp->id ? 'selected' : '' }}>
                                    {{ $esp->descricao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-magnify me-1"></i>
                                Buscar
                            </button>
                            <a href="{{ route('profissionais.index') }}" class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
                                <i class="mdi mdi-refresh me-1"></i>
                                Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
