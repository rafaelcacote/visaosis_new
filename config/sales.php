<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Perfis que podem conceder desconto sem senha de supervisor
    |--------------------------------------------------------------------------
    |
    | Nomes ou short_names cadastrados no Cerberus (seguranca.profiles).
    | A comparação ignora maiúsculas/minúsculas e também aceita correspondência
    | parcial (ex.: "Gerente de Loja" contém "gerente").
    |
    */
    'discount_privileged_profiles' => [
        'administrador',
        'admin',
        'gerente',
        'manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tempo de validade da autorização de desconto (minutos)
    |--------------------------------------------------------------------------
    */
    'discount_authorization_ttl_minutes' => 30,

];
