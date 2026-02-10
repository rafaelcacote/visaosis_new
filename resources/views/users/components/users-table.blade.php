<!-- Lista de usuários -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-account-off text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum usuário encontrado</h5>
                        <p class="text-muted">Comece criando um novo usuário ou ajuste os filtros de busca.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Localizações</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    @include('users.components.user-table-row', ['user' => $user])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
