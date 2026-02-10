<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body user-filters">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="search" class="form-label mb-1">Buscar</label>
                        <input type="text" class="form-control form-control id="search" name="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nome, email ou CPF...">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="status" class="form-label mb-1">Status</label>
                        <select class="form-select form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label mb-1 d-block" aria-hidden="true">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill" style="min-width: 0;">
                                <i class="mdi mdi-magnify me-1"></i>
                                Buscar
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary flex-fill" style="min-width: 0;">
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
