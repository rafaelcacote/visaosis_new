<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body especialidade-filters">
                <form method="GET" action="{{ route('especialidades.index') }}" class="row g-3 align-items-end form-aligned-sm js-list-filter-form">
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Descrição">
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-magnify me-1"></i>
                                Buscar
                            </button>
                            <a href="{{ route('especialidades.index') }}" class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
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
