<!-- Lista de laboratórios -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($laboratorios->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-flask-outline text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum laboratório encontrado</h5>
                        <p class="text-muted">Comece cadastrando um novo laboratório ou ajuste os filtros de busca.</p>
                        @if(!empty($filters['search']))
                            <a href="{{ route('laboratorios.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('laboratorios.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Laboratório
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Razão social / Nome fantasia</th>
                                    <th>CNPJ</th>
                                    <th>Contato</th>
                                    <th>Status</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laboratorios as $laboratorio)
                                    @include('laboratorios.components.laboratorio-table-row', ['laboratorio' => $laboratorio])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $laboratorios->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
