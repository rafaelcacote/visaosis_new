<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Se não há valor, não valida (deixa para outros rules como required)
        if (empty($value)) {
            return;
        }

        // Remove formatação do CPF
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            $fail('O :attribute deve conter exatamente 11 dígitos.');
            return;
        }

        // Verifica se não são todos dígitos iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        // Cálculo dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('O :attribute informado é inválido.');
                return;
            }
        }
    }
}
