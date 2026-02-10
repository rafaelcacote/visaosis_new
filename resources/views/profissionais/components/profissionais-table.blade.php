<!-- Lista de profissionais -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($profissionais->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-account-off text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum profissional encontrado</h5>
                        <p class="text-muted">Comece criando um novo profissional ou ajuste os filtros de busca.</p>
                        @if(!empty($filters['search']))
                            <a href="{{ route('profissionais.index') }}" class="btn btn-outline-primary mt-3">
                                <i class="mdi mdi-arrow-left me-2"></i>
                                Voltar à lista completa
                            </a>
                        @else
                            <a href="{{ route('profissionais.create') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus me-2"></i>
                                Cadastrar Profissional
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Profissional</th>
                                    <th>Especialidade</th>
                                    <th>CPF</th>
                                    <th>Contato</th>
                                    <th>Status</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profissionais as $profissional)
                                    @include('profissionais.components.profissional-table-row', ['profissional' => $profissional])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-3">
                        {{ $profissionais->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
