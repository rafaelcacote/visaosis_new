<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body user-filters">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3 form-aligned-sm js-list-filter-form">
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nome, email ou CPF...">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="" {{ ($filters['status'] ?? '') === '' ? 'selected' : '' }}>Todos</option>
                            <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 d-flex flex-column">
                        <label class="form-label invisible user-select-none" aria-hidden="true">.</label>
                        <div class="d-flex align-items-end flex-grow-1">
                            <div class="d-flex gap-2 align-items-end flex-wrap">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Buscar
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
