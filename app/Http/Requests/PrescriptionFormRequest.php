<?php

namespace App\Http\Requests;

use App\Helpers\AuthHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PrescriptionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $routeName = (string) $this->route()?->getName();

        $decimalFields = [
            'od_esferico',
            'od_cilindrico',
            'oe_esferico',
            'oe_cilindrico',
            'od_adicao',
            'oe_adicao',
            'od_dnp',
            'oe_dnp',
            'od_altura',
            'oe_altura',
        ];

        $normalized = [];
        foreach ($decimalFields as $field) {
            if ($this->filled($field)) {
                $normalized[$field] = str_replace(',', '.', (string) $this->input($field));
            }
        }

        $useProfessionalNormalization = in_array($routeName, ['professional.storeNewPrescription', 'professional.savePrescriptionDraft'], true);

        if ($useProfessionalNormalization) {
            $normalized['od_esferico'] = $this->normalizeEsfericoProfessional($normalized['od_esferico'] ?? $this->input('od_esferico'));
            $normalized['oe_esferico'] = $this->normalizeEsfericoProfessional($normalized['oe_esferico'] ?? $this->input('oe_esferico'));
        } else {
            $normalized['od_esferico'] = $this->normalizeEsfericoPessoa($normalized['od_esferico'] ?? $this->input('od_esferico'));
            $normalized['oe_esferico'] = $this->normalizeEsfericoPessoa($normalized['oe_esferico'] ?? $this->input('oe_esferico'));
        }

        $normalized['od_cilindrico'] = $this->normalizeCilindrico($normalized['od_cilindrico'] ?? $this->input('od_cilindrico'));
        $normalized['oe_cilindrico'] = $this->normalizeCilindrico($normalized['oe_cilindrico'] ?? $this->input('oe_cilindrico'));

        $this->merge($normalized);
    }

    public function rules(): array
    {
        $routeName = (string) $this->route()?->getName();

        $rules = [
            'od_esferico' => ['nullable', 'regex:/^-?\d+(\.\d{1,2})?$/'],
            'od_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'od_eixo' => ['nullable', 'integer', 'between:0,180'],
            'od_acuidade' => ['nullable', 'string', 'max:50'],
            'od_dnp' => ['nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'between:12,80'],
            'od_altura' => ['nullable', 'numeric', 'between:15,40'],
            'od_adicao' => ['nullable', 'numeric', 'between:0.75,3.5', 'regex:/^\+?\d+(\.\d{1,2})?$/'],

            'oe_esferico' => ['nullable', 'regex:/^-?\d+(\.\d{1,2})?$/'],
            'oe_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'oe_eixo' => ['nullable', 'integer', 'between:0,180'],
            'oe_acuidade' => ['nullable', 'string', 'max:50'],
            'oe_dnp' => ['nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'between:12,80'],
            'oe_altura' => ['nullable', 'numeric', 'between:15,40'],
            'oe_adicao' => ['nullable', 'numeric', 'between:0.75,3.5', 'regex:/^\+?\d+(\.\d{1,2})?$/'],

            'tipo_lente' => ['nullable', 'string', 'max:100'],
            'diagnostico' => ['nullable', 'string', 'max:255'],
            'observacoes_receita' => ['nullable', 'string', 'max:1000'],
            'recomendacoes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($routeName === 'professional.storeNewPrescription') {
            $tenantId = AuthHelper::tenantId() ?? 1;
            $locationId = AuthHelper::locationId() ?? 1;

            $rules = array_merge(
                [
                    'nome' => ['required', 'string', 'max:255'],
                    'cpf' => ['nullable', 'string', 'max:14'],
                    'telefone' => ['nullable', 'string', 'max:15'],
                    'email' => ['nullable', 'email'],
                    'profissional_id' => [
                        'nullable',
                        'integer',
                        Rule::exists('profissional', 'id')->where(function ($query) use ($tenantId, $locationId) {
                            $query
                                ->where('tenant_id', $tenantId)
                                ->where('location_id', $locationId)
                                ->where('ativo', true)
                                ->whereNull('deleted_at');
                        }),
                    ],
                ],
                $rules,
            );

            $rules['validade_dias'] = ['nullable', 'integer', 'in:180,365'];
        } elseif (in_array($routeName, ['pessoas.receitas.store', 'pessoas.receitas.update'], true)) {
            $rules = array_merge(
                [
                    'especialista_externo' => ['required', 'string', 'max:255'],
                ],
                $rules,
            );
            $rules['validade_dias'] = ['nullable', 'integer', 'in:30,90,180,365'];
        } elseif ($routeName === 'professional.savePrescriptionDraft') {
            $rules['validade_dias'] = ['nullable', 'integer', 'in:180,365'];
        } else {
            $rules['validade_dias'] = ['nullable', 'integer', 'in:30,90,180,365'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'od_esferico.regex' => 'O campo OD Esférico deve ser PL ou um número com até 2 casas decimais.',
            'oe_esferico.regex' => 'O campo OE Esférico deve ser PL ou um número com até 2 casas decimais.',
            'regex' => 'O campo :attribute deve ser um número com até 2 casas decimais.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'between' => 'O campo :attribute deve estar entre :min e :max.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'in' => 'O campo :attribute possui um valor inválido.',
            'max' => 'O campo :attribute não pode exceder :max caracteres.',
            'profissional_id.exists' => 'O profissional selecionado é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'Nome',
            'cpf' => 'CPF',
            'telefone' => 'Telefone',
            'email' => 'E-mail',
            'profissional_id' => 'Profissional',
            'especialista_externo' => 'Nome do profissional que prescreveu',
            'od_esferico' => 'OD Esférico',
            'od_cilindrico' => 'OD Cilíndrico',
            'od_eixo' => 'OD Eixo',
            'od_acuidade' => 'OD AV (Acuidade)',
            'od_dnp' => 'OD DNP',
            'od_altura' => 'OD Altura',
            'od_adicao' => 'OD Adição',
            'oe_esferico' => 'OE Esférico',
            'oe_cilindrico' => 'OE Cilíndrico',
            'oe_eixo' => 'OE Eixo',
            'oe_acuidade' => 'OE AV (Acuidade)',
            'oe_dnp' => 'OE DNP',
            'oe_altura' => 'OE Altura',
            'oe_adicao' => 'OE Adição',
            'tipo_lente' => 'Tipo de Lente',
            'validade_dias' => 'Validade',
            'diagnostico' => 'Diagnóstico',
            'recomendacoes' => 'Recomendações',
            'observacoes_receita' => 'Observações da Receita',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $routeName = (string) $this->route()?->getName();

        if ($routeName === 'professional.savePrescriptionDraft') {
            $id = $this->route('id');
            $response = redirect()
                ->route('professional.consultation', ['id' => $id])
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'prescription');

            throw new HttpResponseException($response);
        }

        parent::failedValidation($validator);
    }

    private function normalizeEsfericoProfessional(mixed $value): ?string
    {
        $raw = is_string($value) ? trim($value) : '';
        if ($raw === '') {
            return '0';
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $sign = $m[1] === '-' ? '-' : '';
            $formatted = number_format($num, 2, '.', '');
            return $sign . $formatted;
        }

        return $raw;
    }

    private function normalizeEsfericoPessoa(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = is_string($value) ? trim($value) : (string) $value;
        if ($raw === '') {
            return null;
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $sign = $m[1] === '-' ? '-' : '';
            $formatted = number_format($num, 2, '.', '');
            return $sign . $formatted;
        }

        return $raw;
    }

    private function normalizeCilindrico(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = is_string($value) ? trim($value) : (string) $value;
        if ($raw === '') {
            return null;
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $formatted = number_format($num, 2, '.', '');
            return '-' . $formatted;
        }

        return $raw;
    }
}
