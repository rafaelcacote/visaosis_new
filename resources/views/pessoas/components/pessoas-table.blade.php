<!-- Lista de clientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($pessoas->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-account-off text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum cliente encontrado</h5>
                        <p class="text-muted">
                            Comece cadastrando um novo cliente ou ajuste os filtros de busca.
                        </p>
                        @if (!empty($search))
                            <a href="{{ route('pessoas.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('pessoas.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Cliente
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive list-actions-table-wrap">
                        <table class="table table-hover list-actions-table">
                            <thead>
                                <tr>
                                    <th class="list-actions-col-descricao">Cliente</th>
                                    <th class="d-none d-md-table-cell">CPF</th>
                                    <th class="d-none d-md-table-cell">Contato</th>
                                    <th class="d-none d-md-table-cell">Status</th>
                                    <th class="list-actions-col-acoes">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pessoas as $pessoa)
                                    @include('pessoas.components.pessoa-table-row', ['pessoa' => $pessoa])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $pessoas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
