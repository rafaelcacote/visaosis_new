<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body pessoa-filters">
                <form method="GET" action="{{ route('pessoas.index') }}" class="row g-3 align-items-end form-aligned-sm js-list-filter-form">
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="search"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Nome, CPF ou e-mail"
                        >
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="todos" {{ ($status ?? 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="ativo" {{ ($status ?? '') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                            <option value="inativo" {{ ($status ?? '') === 'inativo' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-magnify me-1"></i>
                                Buscar
                            </button>
                            <a href="{{ route('pessoas.index') }}" class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
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
