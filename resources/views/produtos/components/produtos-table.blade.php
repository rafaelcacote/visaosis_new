<!-- Lista de produtos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($produtos->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-package-variant text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum produto encontrado</h5>
                        <p class="text-muted">Cadastre um novo produto ou ajuste os filtros de busca.</p>
                        @if(!empty($filters['search']) || !empty($filters['categoria']) || $filters['status'] !== '')
                            <a href="{{ route('produtos.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('produtos.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Produto
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Preços</th>
                                    <th>Status</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtos as $produto)
                                    @include('produtos.components.produto-table-row', ['produto' => $produto])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $produtos->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
