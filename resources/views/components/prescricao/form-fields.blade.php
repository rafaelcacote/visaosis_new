@php
    $isEditing = $isEditing ?? false;
    $prescricaoForm = $prescricaoForm ?? null;
    $includePerto = $includePerto ?? true;
    $validadeOptions = $validadeOptions ?? [
        '365' => '1 Ano',
        '180' => '6 Meses',
    ];
@endphp

<div class="table-responsive mb-3">
    <table class="table table-bordered table-sm align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center">Esférico</th>
                <th class="text-center">Cilíndrico</th>
                <th class="text-center">Eixo</th>
                <th class="text-center">AV </th>
                <th class="text-center">DNP</th>
                <th class="text-center">Altura</th>
                <th class="text-center">Adição</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td rowspan="2" class="text-center fw-bold"
                    style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.45);">
                    LONGE
                </td>
                <td class="text-center fw-semibold" style="white-space: nowrap;">
                    <i class="mdi mdi-eye-outline me-1"></i>OD
                </td>
                <td>
                    <input type="text" class="form-control @error('od_esferico') is-invalid @enderror"
                        name="od_esferico"
                        value="{{ old('od_esferico', $isEditing && $prescricaoForm ? $prescricaoForm->esfera_od : null) }}"
                        placeholder="+/-0.00">
                    @error('od_esferico')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_cilindrico') is-invalid @enderror"
                        name="od_cilindrico"
                        value="{{ old('od_cilindrico', $isEditing && $prescricaoForm ? $prescricaoForm->cilindro_od : null) }}"
                        placeholder="-0.00">
                    @error('od_cilindrico')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_eixo') is-invalid @enderror" name="od_eixo"
                        value="{{ old('od_eixo', $isEditing && $prescricaoForm ? $prescricaoForm->eixo_od : null) }}"
                        placeholder="0-180">
                    @error('od_eixo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_acuidade') is-invalid @enderror"
                        name="od_acuidade"
                        value="{{ old('od_acuidade', $isEditing && $prescricaoForm ? $prescricaoForm->acuidade_od : null) }}"
                        placeholder="20/20">
                    @error('od_acuidade')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_dnp') is-invalid @enderror" name="od_dnp"
                        value="{{ old('od_dnp', $isEditing && $prescricaoForm ? $prescricaoForm->dnp_od : null) }}"
                        placeholder="62">
                    @error('od_dnp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_altura') is-invalid @enderror" name="od_altura"
                        value="{{ old('od_altura', $isEditing && $prescricaoForm ? $prescricaoForm->altura_od : null) }}"
                        placeholder="0.00">
                    @error('od_altura')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('od_adicao') is-invalid @enderror" name="od_adicao"
                        value="{{ old('od_adicao', $isEditing && $prescricaoForm ? $prescricaoForm->adicao_od : null) }}"
                        placeholder="+0.00">
                    @error('od_adicao')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
            </tr>
            <tr>
                <td class="text-center fw-semibold" style="white-space: nowrap;">
                    <i class="mdi mdi-eye-outline me-1"></i>OE
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_esferico') is-invalid @enderror"
                        name="oe_esferico"
                        value="{{ old('oe_esferico', $isEditing && $prescricaoForm ? $prescricaoForm->esfera_oe : null) }}"
                        placeholder="+/-0.00">
                    @error('oe_esferico')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_cilindrico') is-invalid @enderror"
                        name="oe_cilindrico"
                        value="{{ old('oe_cilindrico', $isEditing && $prescricaoForm ? $prescricaoForm->cilindro_oe : null) }}"
                        placeholder="-0.00">
                    @error('oe_cilindrico')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_eixo') is-invalid @enderror" name="oe_eixo"
                        value="{{ old('oe_eixo', $isEditing && $prescricaoForm ? $prescricaoForm->eixo_oe : null) }}"
                        placeholder="0-180">
                    @error('oe_eixo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_acuidade') is-invalid @enderror"
                        name="oe_acuidade"
                        value="{{ old('oe_acuidade', $isEditing && $prescricaoForm ? $prescricaoForm->acuidade_oe : null) }}"
                        placeholder="20/20">
                    @error('oe_acuidade')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_dnp') is-invalid @enderror" name="oe_dnp"
                        value="{{ old('oe_dnp', $isEditing && $prescricaoForm ? $prescricaoForm->dnp_oe : null) }}"
                        placeholder="62">
                    @error('oe_dnp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_altura') is-invalid @enderror"
                        name="oe_altura"
                        value="{{ old('oe_altura', $isEditing && $prescricaoForm ? $prescricaoForm->altura_oe : null) }}"
                        placeholder="0.00">
                    @error('oe_altura')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control @error('oe_adicao') is-invalid @enderror"
                        name="oe_adicao"
                        value="{{ old('oe_adicao', $isEditing && $prescricaoForm ? $prescricaoForm->adicao_oe : null) }}"
                        placeholder="+0.00">
                    @error('oe_adicao')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </td>
            </tr>
        </tbody>
    </table>
</div>

@if ($includePerto)
    <div class="table-responsive mb-3">
        <table class="table table-sm align-middle">

            <tbody>
                <tr>
                    <td rowspan="2" class="text-center fw-bold"
                        style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.45);">
                        PERTO
                    </td>
                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                        <i class="mdi mdi-eye-outline me-1"></i>OD
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_esferico_perto') is-invalid @enderror"
                            name="od_esferico_perto"
                            value="{{ old('od_esferico_perto', $isEditing && $prescricaoForm ? $prescricaoForm->esfera_od_perto : null) }}"
                            placeholder="+/-0.00">
                        @error('od_esferico_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_cilindrico_perto') is-invalid @enderror"
                            name="od_cilindrico_perto"
                            value="{{ old('od_cilindrico_perto', $isEditing && $prescricaoForm ? $prescricaoForm->cilindro_od_perto : null) }}"
                            placeholder="-0.00">
                        @error('od_cilindrico_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_eixo_perto') is-invalid @enderror"
                            name="od_eixo_perto"
                            value="{{ old('od_eixo_perto', $isEditing && $prescricaoForm ? $prescricaoForm->eixo_od_perto : null) }}"
                            placeholder="0-180">
                        @error('od_eixo_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_acuidade_perto') is-invalid @enderror"
                            name="od_acuidade_perto"
                            value="{{ old('od_acuidade_perto', $isEditing && $prescricaoForm ? $prescricaoForm->acuidade_od_perto : null) }}"
                            placeholder="20/20">
                        @error('od_acuidade_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_dnp_perto') is-invalid @enderror"
                            name="od_dnp_perto"
                            value="{{ old('od_dnp_perto', $isEditing && $prescricaoForm ? $prescricaoForm->dnp_od_perto : null) }}"
                            placeholder="62">
                        @error('od_dnp_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_altura_perto') is-invalid @enderror"
                            name="od_altura_perto"
                            value="{{ old('od_altura_perto', $isEditing && $prescricaoForm ? $prescricaoForm->altura_od_perto : null) }}"
                            placeholder="0.00">
                        @error('od_altura_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('od_adicao_perto') is-invalid @enderror"
                            name="od_adicao_perto"
                            value="{{ old('od_adicao_perto', $isEditing && $prescricaoForm ? $prescricaoForm->adicao_od_perto : null) }}"
                            placeholder="+0.00">
                        @error('od_adicao_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="text-center fw-semibold" style="white-space: nowrap;">
                        <i class="mdi mdi-eye-outline me-1"></i>OE
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_esferico_perto') is-invalid @enderror"
                            name="oe_esferico_perto"
                            value="{{ old('oe_esferico_perto', $isEditing && $prescricaoForm ? $prescricaoForm->esfera_oe_perto : null) }}"
                            placeholder="+/-0.00">
                        @error('oe_esferico_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_cilindrico_perto') is-invalid @enderror"
                            name="oe_cilindrico_perto"
                            value="{{ old('oe_cilindrico_perto', $isEditing && $prescricaoForm ? $prescricaoForm->cilindro_oe_perto : null) }}"
                            placeholder="-0.00">
                        @error('oe_cilindrico_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_eixo_perto') is-invalid @enderror"
                            name="oe_eixo_perto"
                            value="{{ old('oe_eixo_perto', $isEditing && $prescricaoForm ? $prescricaoForm->eixo_oe_perto : null) }}"
                            placeholder="0-180">
                        @error('oe_eixo_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_acuidade_perto') is-invalid @enderror"
                            name="oe_acuidade_perto"
                            value="{{ old('oe_acuidade_perto', $isEditing && $prescricaoForm ? $prescricaoForm->acuidade_oe_perto : null) }}"
                            placeholder="20/20">
                        @error('oe_acuidade_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_dnp_perto') is-invalid @enderror"
                            name="oe_dnp_perto"
                            value="{{ old('oe_dnp_perto', $isEditing && $prescricaoForm ? $prescricaoForm->dnp_oe_perto : null) }}"
                            placeholder="62">
                        @error('oe_dnp_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_altura_perto') is-invalid @enderror"
                            name="oe_altura_perto"
                            value="{{ old('oe_altura_perto', $isEditing && $prescricaoForm ? $prescricaoForm->altura_oe_perto : null) }}"
                            placeholder="0.00">
                        @error('oe_altura_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('oe_adicao_perto') is-invalid @enderror"
                            name="oe_adicao_perto"
                            value="{{ old('oe_adicao_perto', $isEditing && $prescricaoForm ? $prescricaoForm->adicao_oe_perto : null) }}"
                            placeholder="+0.00">
                        @error('oe_adicao_perto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Tipo de Lente</label>
        @php $tipo = old('tipo_lente', $isEditing && $prescricaoForm ? $prescricaoForm->tipo_lente : null) @endphp
        <select class="form-select @error('tipo_lente') is-invalid @enderror" name="tipo_lente">
            <option value="">Selecione</option>
            <option value="Monofocal" {{ $tipo === 'Monofocal' ? 'selected' : '' }}>Monofocal</option>
            <option value="Bifocal" {{ $tipo === 'Bifocal' ? 'selected' : '' }}>Bifocal</option>
            <option value="Multifocal" {{ $tipo === 'Multifocal' ? 'selected' : '' }}>Multifocal</option>
        </select>
        @error('tipo_lente')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Validade</label>
        @php $val = (string) old('validade_dias', $isEditing && $prescricaoForm ? $prescricaoForm->validade_dias : '') @endphp
        <select class="form-select @error('validade_dias') is-invalid @enderror" name="validade_dias">
            <option value="">Selecione</option>
            @foreach ($validadeOptions as $value => $label)
                <option value="{{ $value }}" {{ $val === (string) $value ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
        @error('validade_dias')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Diagnóstico</label>
        <input type="text" class="form-control @error('diagnostico') is-invalid @enderror" name="diagnostico"
            value="{{ old('diagnostico', $isEditing && $prescricaoForm ? $prescricaoForm->diagnostico : null) }}">
        @error('diagnostico')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Recomendações</label>
        <textarea class="form-control @error('recomendacoes') is-invalid @enderror" name="recomendacoes" rows="2">{{ old('recomendacoes', $isEditing && $prescricaoForm ? $prescricaoForm->recomendacoes : null) }}</textarea>
        @error('recomendacoes')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Observações da Receita</label>
        <textarea class="form-control @error('observacoes_receita') is-invalid @enderror" name="observacoes_receita"
            rows="2">{{ old('observacoes_receita', $isEditing && $prescricaoForm ? $prescricaoForm->observacoes : null) }}</textarea>
        @error('observacoes_receita')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
