<!-- Lista de especialidades -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($especialidades->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-stethoscope text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhuma especialidade encontrada</h5>
                        <p class="text-muted">Comece criando uma nova especialidade ou ajuste os filtros de busca.</p>
                        @if (!empty($filters['search']))
                            <a href="{{ route('especialidades.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('especialidades.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Especialidade
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive list-actions-table-wrap">
                        <table class="table table-hover list-actions-table">
                            <thead>
                                <tr>
                                    <th class="list-actions-col-descricao">Descrição</th>
                                    <th class="list-actions-col-acoes">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($especialidades as $especialidade)
                                    @include('especialidades.components.especialidade-table-row', [
                                        'especialidade' => $especialidade,
                                    ])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $especialidades->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
