<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $cnpj = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cnpj) !== 14) {
            $fail('O :attribute deve ter 14 dígitos.');
            return;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        $tamanho = 12;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += intval($numeros[$tamanho - $i]) * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != intval($cnpj[12])) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        $tamanho = 13;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += intval($numeros[$tamanho - $i]) * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != intval($cnpj[13])) {
            $fail('O :attribute informado é inválido.');
        }
    }
}
