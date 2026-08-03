<!-- Lista de categorias -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($categorias->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-tag-off text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhuma categoria encontrada</h5>
                        <p class="text-muted">Comece criando uma nova categoria ou ajuste os filtros de busca.</p>
                        @if (!empty($filters['search']))
                            <a href="{{ route('categorias.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('categorias.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Categoria
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive list-actions-table-wrap">
                        <table class="table table-hover list-actions-table">
                            <thead>
                                <tr>
                                    <th class="list-actions-col-descricao">Descrição</th>
                                    <th class="d-none d-md-table-cell">Status</th>
                                    <th class="list-actions-col-acoes">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                    @include('categorias.components.categoria-table-row', [
                                        'categoria' => $categoria,
                                    ])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-3">
                        {{ $categorias->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
