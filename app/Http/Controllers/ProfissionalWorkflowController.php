<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Consulta; // Import the Consulta model
use App\Models\Encaminhamento;
use App\Models\Especialidade;
use App\Models\Exame;
use App\Models\Pessoa;
use App\Models\Prescricao;
use App\Models\Profissional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfissionalWorkflowController extends Controller
{
    /**
     * Busca de pacientes para o profissional
     */
    public function searchPatient(Request $request)
    {
        $term = $request->get('term');
        $tenantId = AuthHelper::tenantId() ?? 1;
        $locationId = AuthHelper::locationId() ?? 1;

        $pacientes = Pessoa::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->where(function ($query) use ($term) {
                $query->where('nome', 'ilike', "%{$term}%")
                    ->orWhere('cpf', 'like', "%{$term}%")
                    ->orWhere('telefone', 'like', "%{$term}%");
            })
            ->orderBy('nome')
            ->limit(10) // Limitar a 10 resultados
            ->get();

        if ($pacientes->count() > 0) {
            return response()->json([
                'found' => true,
                'count' => $pacientes->count(),
                'multiple' => $pacientes->count() > 1,
                'pacientes' => $pacientes->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nome' => $p->nome,
                        'cpf' => $p->cpf,
                        'cpf_formatado' => $p->cpf_formatado,
                        'telefone' => $p->telefone,
                        'telefone_formatado' => $p->telefone_formatado,
                        'email' => $p->email,
                        'nascimento_em' => $p->nascimento_em ? $p->nascimento_em->format('Y-m-d') : null,
                        'idade' => $p->idade,
                    ];
                }),
                'paciente' => $pacientes->count() === 1 ? [
                    'id' => $pacientes->first()->id,
                    'nome' => $pacientes->first()->nome,
                    'cpf' => $pacientes->first()->cpf,
                    'cpf_formatado' => $pacientes->first()->cpf_formatado,
                    'telefone' => $pacientes->first()->telefone,
                    'telefone_formatado' => $pacientes->first()->telefone_formatado,
                    'email' => $pacientes->first()->email,
                    'nascimento_em' => $pacientes->first()->nascimento_em ? $pacientes->first()->nascimento_em->format('Y-m-d') : null,
                    'idade' => $pacientes->first()->idade,
                ] : null,
            ]);
        }

        return response()->json([
            'found' => false,
            'count' => 0,
            'multiple' => false,
            'pacientes' => [],
            'paciente' => null,
        ]);
    }

    /**
     * Dashboard do profissional
     */
    public function index()
    {
        // Buscar pacientes da fila de atendimento usando o model Consulta
        // com eager loading dos relacionamentos paciente e profissional

        $today = Carbon::now('America/Manaus')->format('Y-m-d');

        $patients = Consulta::with(['paciente', 'profissional'])
            ->where('ativo', true)
            ->whereDate('chegada_em', '=', $today)
            ->whereNull('deleted_at')
            ->orderBy('agendado_em', 'desc')
            ->get();

        $totalToday = $patients->count();
        $waiting = $patients->where('status', Consulta::STATUS_AGUARDANDO)->count();
        $inService = $patients->where('status', Consulta::STATUS_EM_ATENDIMENTO)->count();
        $completed = $patients->where('status', Consulta::STATUS_ATENDIDO)->count();
        $cancelled = $patients->where('status', Consulta::STATUS_CANCELADO)->count();
        $encaminhado = $patients->where('status', Consulta::STATUS_ENCAMINHADO)->count();

        $stats = [
            'total_today' => $totalToday,
            'waiting' => $waiting,
            'in_service' => $inService,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'encaminhado' => $encaminhado,

        ];

        return view('professional.index', compact('patients', 'stats'));
    }

    public function printExamDoc($id)
    {
        $consulta = Consulta::with(['paciente', 'profissional.especialidade'])
            ->where('id', $id)
            ->firstOrFail();

        $exameModel = \App\Models\Exame::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $exameModel) {
            return redirect()->back()->with('error', 'Exame não encontrado.');
        }

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $exame = $exameModel->toArray();
        $exame['data'] = $exameModel->created_at;

        $exame['dados'] = [
            'av_od' => $exameModel->acuidade_od,
            'av_oe' => $exameModel->acuidade_oe,
            'pio_od' => $exameModel->pressao_od,
            'pio_oe' => $exameModel->pressao_oe,
            'fundoscopia' => $exameModel->fundoscopia,
            'anamnese' => $exameModel->anamnese,
            'observacoes' => $exameModel->observacoes,
        ];

        $exame['paciente'] = [
            'nome' => $paciente->nome ?? '',
            'idade' => $paciente->idade ?? null,
            'cpf' => $paciente->cpf_formatado ?? '',
            'telefone' => $paciente->telefone_formatado ?? '',
        ];

        $exame['profissional'] = [
            'nome' => $profissional->nome ?? '',
            'registro_conselho' => $profissional->registro_conselho ?? null,
            'especialidade' => $profissional->especialidade->descricao ?? null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.exame-pdf', compact('exame'));

        return $pdf->stream('exame-' . $consulta->id . '.pdf');
    }

    public function printReferralDoc($id)
    {
        $consulta = Consulta::with(['paciente', 'profissional.especialidade'])
            ->where('id', $id)
            ->firstOrFail();

        $encaminhamentoModel = \App\Models\Encaminhamento::with('especialidade')
            ->where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $encaminhamentoModel) {
            return redirect()->back()->with('error', 'Encaminhamento não encontrado.');
        }

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $referral = $encaminhamentoModel->toArray();
        $referral['data'] = $encaminhamentoModel->created_at;

        $referral['dados'] = [
            'especialidade_destino' => $encaminhamentoModel->especialidade->descricao ?? 'Não informada',
            'usuario_oculos' => $encaminhamentoModel->usuario_oculos,
            'ultima_avaliacao' => $encaminhamentoModel->ultima_avaliacao_em ? \Carbon\Carbon::parse($encaminhamentoModel->ultima_avaliacao_em)->format('d/m/Y') : 'Não informada',
            'hipotese' => $encaminhamentoModel->hipotese,
            'urgencia' => $encaminhamentoModel->urgencia === 'emergencia' ? 'Emergência' : ucfirst($encaminhamentoModel->urgencia),
            'observacoes' => $encaminhamentoModel->observacoes,
        ];

        $referral['paciente'] = [
            'nome' => $paciente->nome ?? '',
            'idade' => $paciente->idade ?? null,
            'cpf' => $paciente->cpf_formatado ?? '',
            'telefone' => $paciente->telefone_formatado ?? '',
        ];

        $referral['profissional'] = [
            'nome' => $profissional->nome ?? '',
            'registro_conselho' => $profissional->registro_conselho ?? null,
            'especialidade' => $profissional->especialidade->descricao ?? null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.referral-pdf', compact('referral'));

        return $pdf->stream('encaminhamento-' . $consulta->id . '.pdf');
    }

    /**
     * Iniciar atendimento do paciente
     */
    public function startConsultation($id)
    {
        $consulta = Consulta::with(['paciente', 'exame', 'prescricao', 'encaminhamento'])
            ->where('id', $id)
            ->first();
        // $patient = Pessoa::find($id);

        // dd($consulta);

        if (! $consulta) {
            return redirect()->route('professional.index')->with('error', 'Paciente não encontrado.');
        }
        $patient = $consulta->paciente;

        // Buscar o último atendimento do paciente
        $ultimaConsulta = Consulta::with(['prescricao'])
            ->where('pessoa_paciente_id', $patient['id'])
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->whereNotNull('atendido_em')
            ->orderByDesc('atendido_em')
            ->first();

        $prescricao = [];
        if ($ultimaConsulta) {
            $prescricao = \App\Models\Prescricao::where('consulta_id', $ultimaConsulta->id)
                ->where('tenant_id', $ultimaConsulta->tenant_id)
                ->whereNull('deleted_at')
                ->first();
        }

        // Buscar histórico de consultas
        $historico = Consulta::with(['profissional'])
            ->where('pessoa_paciente_id', $id)
            ->where('ativo', true)
            ->whereNotNull('atendido_em')
            ->whereNull('deleted_at')
            ->orderByDesc('atendido_em')
            ->get()
            ->map(function ($consulta) {
                $data = '';
                $hora = '';
                if ($consulta->atendido_em) {
                    try {
                        $carbonDate = \Carbon\Carbon::parse($consulta->atendido_em);
                        $data = $carbonDate->format('Y-m-d');
                        $hora = $carbonDate->format('H:i');
                    } catch (\Exception $e) {
                        $data = '';
                        $hora = '';
                    }
                }

                return [
                    'data' => $data,
                    'hora' => $hora,
                    'profissional' => $consulta->profissional ? $consulta->profissional->nome : '',
                    'diagnostico' => $consulta->observacoes,
                    'observacoes' => $consulta->observacoes,
                ];
            });

        // Add: current prescription (draft or saved) for this consulta
        $prescricaoAtual = \App\Models\Prescricao::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        $exame = \App\Models\Exame::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        $encaminhamento = Encaminhamento::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        // Monta os dados do paciente para a view
        $patientData = [
            'id' => $patient->id,
            'nome' => $patient->nome,
            'idade' => $patient->idade,
            'cpf' => $patient->cpf_formatado,
            'telefone' => $patient->telefone_formatado,
            'email' => $patient->email,
            'endereco' => $patient->getEnderecoCompletoAttribute(),
            'motivo' => $consulta->getTipoLabelAttribute(),
            'historico' => $historico,
            'ultima_receita' => $ultimaConsulta ? [
                'data' => \Carbon\Carbon::parse($ultimaConsulta->atendido_em),
                'od_esferico' => optional($prescricao ?? null)->esfera_od,
                'od_cilindrico' => optional($prescricao ?? null)->cilindro_od,
                'od_eixo' => optional($prescricao ?? null)->eixo_od,
                'od_adicao' => optional($prescricao ?? null)->adicao_od,
                'od_dnp' => optional($prescricao ?? null)->dnp_od,
                'od_altura' => optional($prescricao ?? null)->altura_od,
                'oe_esferico' => optional($prescricao ?? null)->esfera_oe,
                'oe_cilindrico' => optional($prescricao ?? null)->cilindro_oe,
                'oe_eixo' => optional($prescricao ?? null)->eixo_oe,
                'oe_adicao' => optional($prescricao ?? null)->adicao_oe,
                'oe_dnp' => optional($prescricao ?? null)->dnp_oe,
                'oe_altura' => optional($prescricao ?? null)->altura_oe,
                'observacoes' => optional($prescricao ?? null)->observacoes,
            ] : null,
            // Add: current prescription values to prefill the form
            'prescricao' => $prescricaoAtual ? [
                'od_esferico' => $prescricaoAtual->esfera_od,
                'od_cilindrico' => $prescricaoAtual->cilindro_od,
                'od_eixo' => $prescricaoAtual->eixo_od,
                'od_dnp' => $prescricaoAtual->dnp_od,
                'od_altura' => $prescricaoAtual->altura_od,
                'od_adicao' => $prescricaoAtual->adicao_od,
                'oe_esferico' => $prescricaoAtual->esfera_oe,
                'oe_cilindrico' => $prescricaoAtual->cilindro_oe,
                'oe_eixo' => $prescricaoAtual->eixo_oe,
                'oe_dnp' => $prescricaoAtual->dnp_oe,
                'oe_altura' => $prescricaoAtual->altura_oe,
                'oe_adicao' => $prescricaoAtual->adicao_oe,
                'tipo_lente' => $prescricaoAtual->tipo_lente,
                'validade_dias' => $prescricaoAtual->validade_dias,
                'diagnostico' => $prescricaoAtual->diagnostico,
                'observacoes_receita' => $prescricaoAtual->observacoes,
                'recomendacoes' => $prescricaoAtual->recomendacoes,
            ] : null,
            'exame' => $exame ? [
                'anamnese' => $exame->anamnese,
                'av_od' => $exame->acuidade_od,
                'av_oe' => $exame->acuidade_oe,
                'pio_od' => $exame->pressao_od,
                'pio_oe' => $exame->pressao_oe,
                'fundoscopia' => $exame->fundoscopia,
                'observacoes' => $exame->observacoes,
            ] : null,

        ];

        $especialidades = Especialidade::ativos()->orderBy('descricao')->get();

        return view('professional.consultation', ['patient' => $patientData], compact('consulta', 'especialidades', 'encaminhamento'));
    }

    /**
     * Gerar receita
     */
    public function generatePrescription(Request $request, $id)
    {
        // Fetch the consultation and related data from DB
        $consulta = Consulta::with(['paciente', 'profissional'])
            ->where('id', $id)
            ->firstOrFail();

        $prescricao = \App\Models\Prescricao::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $dataReceita = $consulta->atendido_em
            ? \Carbon\Carbon::parse($consulta->atendido_em)->format('Y-m-d')
            : \Carbon\Carbon::now('America/Manaus')->format('Y-m-d');

        $prescription = [
            'numero' => 'RX-' . date('Y') . '-' . str_pad($consulta->id, 4, '0', STR_PAD_LEFT),
            'data' => $dataReceita,
            'paciente' => [
                'nome' => $paciente->nome ?? '',
                'idade' => $paciente->idade ?? null,
                'cpf' => $paciente->cpf_formatado ?? '',
                'telefone' => $paciente->telefone_formatado ?? '',
            ],
            'profissional' => [
                'nome' => $profissional->nome ?? '',
                // Optional fields (set if your model has them)
                'registro_conselho' => $profissional->registro_conselho ?? null,
                'especialidade' => $profissional->especialidade->descricao ?? null,
            ],
            'prescricao' => [
                'od_esferico' => $request->input('od_esferico', optional($prescricao)->esfera_od),
                'od_cilindrico' => $request->input('od_cilindrico', optional($prescricao)->cilindro_od),
                'od_eixo' => $request->input('od_eixo', optional($prescricao)->eixo_od),
                'od_dnp' => $request->input('od_dnp', optional($prescricao)->dnp_od),
                'od_altura' => $request->input('od_altura', optional($prescricao)->altura_od),
                'od_adicao' => $request->input('od_adicao', optional($prescricao)->adicao_od),

                'oe_esferico' => $request->input('oe_esferico', optional($prescricao)->esfera_oe),
                'oe_cilindrico' => $request->input('oe_cilindrico', optional($prescricao)->cilindro_oe),
                'oe_eixo' => $request->input('oe_eixo', optional($prescricao)->eixo_oe),
                'oe_dnp' => $request->input('oe_dnp', optional($prescricao)->dnp_oe),
                'oe_altura' => $request->input('oe_altura', optional($prescricao)->altura_oe),
                'oe_adicao' => $request->input('oe_adicao', optional($prescricao)->adicao_oe),

                'tipo_lente' => $request->input('tipo_lente', optional($prescricao)->tipo_lente),
                'validade_dias' => $request->input('validade_dias', optional($prescricao)->validade_dias),
            ],
            'diagnostico' => $request->input('diagnostico', optional($prescricao)->diagnostico),
            'recomendacoes' => $request->input('recomendacoes', optional($prescricao)->recomendacoes),
            'observacoes' => $request->input('observacoes_receita', optional($prescricao)->observacoes),
        ];

        return view('professional.prescription', compact('prescription'));
    }

    /**
     * Enviar receita por WhatsApp
     */
    public function sendWhatsApp(Request $request)
    {
        $phone = $request->input('phone');
        $consultaId = $request->input('consulta_id');

        if (! $phone || ! $consultaId) {
            return response()->json(['success' => false, 'message' => 'Dados incompletos'], 400);
        }

        // Gerar token criptografado
        try {
            $token = encrypt($consultaId);
            // Gerar link para o PDF usando a rota pública com token
            $pdfUrl = route('public.prescription.view', ['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao gerar link seguro'], 500);
        }

        // Limpar telefone (apenas números)
        $phoneClean = preg_replace('/\D/', '', $phone);
        // Adicionar código do país se não tiver (assumindo BR +55)
        if (strlen($phoneClean) <= 11) {
            $phoneClean = '55' . $phoneClean;
        }

        $message = "Olá! Segue o link da sua receita médica: {$pdfUrl}";
        $whatsappUrl = "https://web.whatsapp.com/send?phone={$phoneClean}&text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Web será aberto com a mensagem pronta.',
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    /**
     * Enviar exame por WhatsApp
     */
    public function sendExamWhatsApp(Request $request)
    {
        $phone = $request->input('phone');
        $consultaId = $request->input('consulta_id');

        if (! $phone || ! $consultaId) {
            return response()->json(['success' => false, 'message' => 'Dados incompletos'], 400);
        }

        // Gerar token criptografado
        try {
            $token = encrypt($consultaId);
            // Gerar link para o PDF usando a rota pública com token
            $pdfUrl = route('public.exam.view', ['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao gerar link seguro'], 500);
        }

        // Limpar telefone (apenas números)
        $phoneClean = preg_replace('/\D/', '', $phone);
        // Adicionar código do país se não tiver (assumindo BR +55)
        if (strlen($phoneClean) <= 11) {
            $phoneClean = '55' . $phoneClean;
        }

        $message = "Olá! Segue o link do seu exame de vista: {$pdfUrl}";
        $whatsappUrl = "https://web.whatsapp.com/send?phone={$phoneClean}&text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Web será aberto com a mensagem pronta.',
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    /**
     * Enviar encaminhamento por WhatsApp
     */
    public function sendReferralWhatsApp(Request $request)
    {
        $phone = $request->input('phone');
        $consultaId = $request->input('consulta_id');

        if (! $phone || ! $consultaId) {
            return response()->json(['success' => false, 'message' => 'Dados incompletos'], 400);
        }

        // Gerar token criptografado
        try {
            $token = encrypt($consultaId);
            // Gerar link para o PDF usando a rota pública com token
            $pdfUrl = route('public.referral.view', ['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao gerar link seguro'], 500);
        }

        // Limpar telefone (apenas números)
        $phoneClean = preg_replace('/\D/', '', $phone);
        // Adicionar código do país se não tiver (assumindo BR +55)
        if (strlen($phoneClean) <= 11) {
            $phoneClean = '55' . $phoneClean;
        }

        $message = "Olá! Segue o link do seu termo de encaminhamento: {$pdfUrl}";
        $whatsappUrl = "https://web.whatsapp.com/send?phone={$phoneClean}&text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Web será aberto com a mensagem pronta.',
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    /**
     * Encaminhar paciente
     */
    public function referPatient(Request $request, $id)
    {
        try {
            $consulta = Consulta::with(['paciente', 'profissional'])
                ->where('id', $id)
                ->firstOrFail();

            $usuarioOculos = $request->input('usuario_ocular') === 'sim' ? 1 : 0;
            $ultimaAvaliacao = $request->input('data')
                ? \Carbon\Carbon::parse($request->input('data'))->format('Y-m-d')
                : \Carbon\Carbon::now('America/Manaus')->format('Y-m-d');

            $encaminhamento = Encaminhamento::updateOrCreate(
                [
                    'tenant_id' => AuthHelper::tenantId(),
                    'consulta_id' => $consulta->id,
                ],
                [
                    'especialidade_id' => (int) $request->input('especialidade'),
                    'user_id' => AuthHelper::userId(),
                    'usuario_oculos' => $usuarioOculos,
                    'ultima_avaliacao_em' => $ultimaAvaliacao,
                    'hipotese' => $request->input('motivo'),
                    'urgencia' => $request->input('urgencia', 'normal'),
                    'observacoes' => $request->input('observacoes'),
                    'ativo' => true,
                    'location_id' => AuthHelper::locationId(),
                ]
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paciente encaminhado com sucesso!',
                    'referral' => [
                        'consulta_id' => $consulta->id,
                        'especialidade_id' => $encaminhamento->especialidade_id,
                        'urgencia' => $encaminhamento->urgencia,
                    ],
                ]);
            }

            return redirect()->back()->with('success', 'Encaminhamento salvo com sucesso!')->with('active_tab', 'referral');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao encaminhar paciente: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', 'Erro ao encaminhar paciente: ' . $e->getMessage())->with('active_tab', 'referral');
        }
    }

    /**
     * Finalizar atendimento
     */
    public function finishConsultation(Request $request, $id)
    {
        try {
            $consulta = Consulta::with(['encaminhamento'])
                ->where('id', $id)
                ->firstOrFail();

            $consulta->atendido_em = \Carbon\Carbon::now('America/Manaus');
            $retornoSelecionado = $request->input('retorno');
            $diasRetorno = null;
            if ($retornoSelecionado === 'outro') {
                $diasRetorno = (int) $request->input('retorno_outro_dias');
            } elseif (is_numeric($retornoSelecionado)) {
                $diasRetorno = (int) $retornoSelecionado;
            }
            $consulta->retorno_em = ($diasRetorno && $diasRetorno > 0)
                ? \Carbon\Carbon::now('America/Manaus')->addDays($diasRetorno)->format('Y-m-d')
                : null;
            $consulta->observacoes = $request->input('resumo');

            $hasEncaminhamento = Encaminhamento::where('consulta_id', $consulta->id)
                ->where('tenant_id', AuthHelper::tenantId())
                ->whereNull('deleted_at')
                ->exists();

            $consulta->status = $hasEncaminhamento
                ? Consulta::STATUS_ENCAMINHADO
                : Consulta::STATUS_ATENDIDO;

            $consulta->save();

            return redirect()->route('professional.index')->with('success', 'Consulta finalizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao finalizar consulta: ' . $e->getMessage());
        }
    }

    /**
     * Histórico do paciente
     */
    public function patientHistory($id)
    {
        $patient = Pessoa::find($id);

        if (! $patient) {
            return response()->json(['error' => 'Paciente não encontrado'], 404);
        }

        $consultations = Consulta::with([
            'profissional',
            'paciente',
        ])
            ->where('pessoa_paciente_id', $id)
            ->where('ativo', true)
            ->whereNotNull('atendido_em')
            ->whereNull('deleted_at')
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->orderByDesc('atendido_em')
            ->get();

        $history = $consultations->map(function ($consulta) {
            $data = '';
            $hora = '';
            if ($consulta->atendido_em) {
                try {
                    $carbonDate = \Carbon\Carbon::parse($consulta->atendido_em);
                    $data = $consulta->atendido_em;
                    $hora = $carbonDate->format('H:i');
                } catch (\Exception $e) {
                    $data = '';
                    $hora = '';
                }
            }

            return [
                'id' => $consulta->id, // Add ID for generating links
                'data' => $data,
                'hora' => $hora,
                'profissional' => $consulta->profissional ? $consulta->profissional->nome : '',
                'tipo' => $consulta->getTipoLabelAttribute(),
                'observacoes' => $consulta->observacoes,
            ];
        });

        $ultimaConsulta = Consulta::getUltimaConsulta($patient->id);

        $patientData = [
            'id' => $patient->id,
            'nome' => $patient->nome,
            'idade' => $patient->idade,
            'cpf' => $patient->cpf_formatado,
            'telefone' => $patient->telefone_formatado,
            'email' => $patient->email,
            'ultima_consulta' => $ultimaConsulta,
        ];

        \Log::info('History contents:', ['history' => $history]);

        return response()->json([
            'patient' => $patientData,
            'history' => $history,
        ]);
    }

    public function patientHistoryFull(Request $request, $id)
    {
        $returnTo = $request->query('return_to', 'queue');
        $consultaId = $request->query('consulta_id');

        $patientModel = Pessoa::find($id);
        if (! $patientModel) {
            abort(404, 'Paciente não encontrado');
        }

        $consultations = Consulta::with(['profissional', 'prescricao', 'exame', 'encaminhamento'])
            ->where('pessoa_paciente_id', $id)
            ->where('ativo', true)
            ->whereNotNull('atendido_em')
            ->whereNull('deleted_at')
            ->orderByDesc('atendido_em')
            ->get();

        $history = $consultations->map(function ($consulta) {
            $data = '';
            $hora = '';
            if ($consulta->atendido_em) {
                try {
                    $carbonDate = \Carbon\Carbon::parse($consulta->atendido_em, 'America/Manaus');
                    $data = $carbonDate->format('Y-m-d');
                    $hora = $carbonDate->format('H:i');
                } catch (\Exception $e) {
                    $data = '';
                    $hora = '';
                }
            }
            // dd($consulta);
            $pres = $consulta->prescricao ?: Prescricao::where('consulta_id', $consulta->id)
                ->where('tenant_id', $consulta->tenant_id)
                ->whereNull('deleted_at')
                ->first();
            $exame = $consulta->exame ?: Exame::where('consulta_id', $consulta->id)
                ->where('tenant_id', $consulta->tenant_id)
                ->whereNull('deleted_at')
                ->first();
            $ref = $consulta->encaminhamento ?: Encaminhamento::where('consulta_id', $consulta->id)
                ->where('tenant_id', $consulta->tenant_id)
                ->whereNull('deleted_at')
                ->first();
            $tipoLabel = $consulta->tipo === Consulta::TIPO_CONSULTA ? 'Consulta' : ($consulta->tipo === Consulta::TIPO_RETORNO ? 'Retorno' : 'Conferência');

            return [
                'id' => $consulta->id, // Add ID for generating links
                'data' => $data,
                'hora' => $hora,
                'tipo' => $tipoLabel,
                'profissional' => $consulta->profissional->nome ?? '',
                'receita' => $pres ? true : false,
                'od_esferico' => optional($pres)->esfera_od,
                'oe_esferico' => optional($pres)->esfera_oe,
                'od_cilindrico' => optional($pres)->cilindro_od,
                'od_eixo' => optional($pres)->eixo_od,
                'oe_cilindrico' => optional($pres)->cilindro_oe,
                'oe_eixo' => optional($pres)->eixo_oe,
                'od_adicao' => optional($pres)->adicao_od,
                'oe_adicao' => optional($pres)->adicao_oe,
                'od_dnp' => optional($pres)->dnp_od,
                'oe_dnp' => optional($pres)->dnp_oe,
                'od_altura' => optional($pres)->altura_od,
                'oe_altura' => optional($pres)->altura_oe,
                'diagnostico' => optional($pres)->diagnostico,
                'observacoes_receita' => optional($pres)->observacoes,
                'observacoes' => $consulta->observacoes,

                // Exame
                'exame' => $exame ? true : false,
                'av_od' => optional($exame)->acuidade_od,
                'av_oe' => optional($exame)->acuidade_oe,
                'pio_od' => optional($exame)->pressao_od,
                'pio_oe' => optional($exame)->pressao_oe,
                'fundoscopia' => optional($exame)->fundoscopia,
                'anamnese' => optional($exame)->anamnese,
                'observacoes_exame' => optional($exame)->observacoes,

                // Encaminhamento
                'encaminhamento' => $ref ? true : false,
                'esp_descricao' => $ref ? (optional($ref->especialidade)->descricao ?? null) : null,
                'hipotese' => optional($ref)->hipotese,
                'urgencia' => optional($ref)->urgencia,
                'ultima_avaliacao_format' => ($ref && $ref->ultima_avaliacao_em) ? \Carbon\Carbon::parse($ref->ultima_avaliacao_em)->setTimezone('America/Manaus')->format('d/m/Y H:i') : null,
                'observacoes_encaminhamento' => optional($ref)->observacoes,
            ];
        });

        $ultimaConsulta = Consulta::getUltimaConsulta($patientModel->id);

        $patient = [
            'id' => $patientModel->id,
            'nome' => $patientModel->nome,
            'idade' => $patientModel->idade,
            'cpf' => $patientModel->cpf_formatado,
            'telefone' => $patientModel->telefone_formatado,
            'email' => $patientModel->email,
            'ultima_consulta' => $ultimaConsulta,
        ];

        // dd($history);

        return view('professional.patient-history-full', compact('patient', 'history', 'returnTo', 'consultaId'));
    }

    /**
     * Imprimir receita via token público
     */
    public function viewPrescriptionByToken($token)
    {
        try {
            $id = decrypt($token);
        } catch (\Exception $e) {
            abort(404);
        }

        $consulta = Consulta::with(['paciente', 'profissional'])
            ->where('id', $id)
            ->firstOrFail();

        $prescricao = \App\Models\Prescricao::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $dataReceita = $consulta->atendido_em
            ? \Carbon\Carbon::parse($consulta->atendido_em)->format('Y-m-d')
            : \Carbon\Carbon::now('America/Manaus')->format('Y-m-d');

        $prescription = [
            'numero' => 'RX-' . date('Y') . '-' . str_pad($consulta->id, 4, '0', STR_PAD_LEFT),
            'data' => $dataReceita,
            'paciente' => [
                'nome' => $paciente->nome ?? '',
                'idade' => $paciente->idade ?? null,
                'cpf' => $paciente->cpf_formatado ?? '',
                'telefone' => $paciente->telefone_formatado ?? '',
            ],
            'profissional' => [
                'nome' => $profissional->nome ?? '',
                'registro_conselho' => $profissional->registro_conselho ?? null,
                'especialidade' => $profissional->especialidade->descricao ?? null,
            ],
            'prescricao' => [
                'od_esferico' => optional($prescricao)->esfera_od,
                'od_cilindrico' => optional($prescricao)->cilindro_od,
                'od_eixo' => optional($prescricao)->eixo_od,
                'od_dnp' => optional($prescricao)->dnp_od,
                'od_altura' => optional($prescricao)->altura_od,
                'od_adicao' => optional($prescricao)->adicao_od,

                'oe_esferico' => optional($prescricao)->esfera_oe,
                'oe_cilindrico' => optional($prescricao)->cilindro_oe,
                'oe_eixo' => optional($prescricao)->eixo_oe,
                'oe_dnp' => optional($prescricao)->dnp_oe,
                'oe_altura' => optional($prescricao)->altura_oe,
                'oe_adicao' => optional($prescricao)->adicao_oe,

                'tipo_lente' => optional($prescricao)->tipo_lente,
                'validade_dias' => optional($prescricao)->validade_dias,
            ],
            'diagnostico' => optional($prescricao)->diagnostico,
            'recomendacoes' => optional($prescricao)->recomendacoes,
            'observacoes' => optional($prescricao)->observacoes,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.prescription-pdf', compact('prescription'));

        return $pdf->stream('receita.pdf');
    }

    public function viewExamByToken($token)
    {
        try {
            $id = decrypt($token);
        } catch (\Exception $e) {
            abort(404);
        }

        $consulta = Consulta::with(['paciente', 'profissional.especialidade'])
            ->where('id', $id)
            ->firstOrFail();

        $exameModel = \App\Models\Exame::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $exameModel) {
            abort(404, 'Exame não encontrado.');
        }

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $exame = $exameModel->toArray();
        $exame['data'] = $exameModel->created_at;

        $exame['dados'] = [
            'av_od' => $exameModel->acuidade_od,
            'av_oe' => $exameModel->acuidade_oe,
            'pio_od' => $exameModel->pressao_od,
            'pio_oe' => $exameModel->pressao_oe,
            'fundoscopia' => $exameModel->fundoscopia,
            'anamnese' => $exameModel->anamnese,
            'observacoes' => $exameModel->observacoes,
        ];

        $exame['paciente'] = [
            'nome' => $paciente->nome ?? '',
            'idade' => $paciente->idade ?? null,
            'cpf' => $paciente->cpf_formatado ?? '',
            'telefone' => $paciente->telefone_formatado ?? '',
        ];

        $exame['profissional'] = [
            'nome' => $profissional->nome ?? '',
            'registro_conselho' => $profissional->registro_conselho ?? null,
            'especialidade' => $profissional->especialidade->descricao ?? null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.exame-pdf', compact('exame'));

        return $pdf->stream('exame-' . $consulta->id . '.pdf');
    }

    public function viewReferralByToken($token)
    {
        try {
            $id = decrypt($token);
        } catch (\Exception $e) {
            abort(404);
        }

        $consulta = Consulta::with(['paciente', 'profissional.especialidade'])
            ->where('id', $id)
            ->firstOrFail();

        $encaminhamentoModel = \App\Models\Encaminhamento::with('especialidade')
            ->where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $encaminhamentoModel) {
            abort(404, 'Encaminhamento não encontrado.');
        }

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $referral = $encaminhamentoModel->toArray();
        $referral['data'] = $encaminhamentoModel->created_at;

        $referral['dados'] = [
            'especialidade_destino' => $encaminhamentoModel->especialidade->descricao ?? 'Não informada',
            'usuario_oculos' => $encaminhamentoModel->usuario_oculos,
            'ultima_avaliacao' => $encaminhamentoModel->ultima_avaliacao_em ? \Carbon\Carbon::parse($encaminhamentoModel->ultima_avaliacao_em)->format('d/m/Y') : 'Não informada',
            'hipotese' => $encaminhamentoModel->hipotese,
            'urgencia' => $encaminhamentoModel->urgencia === 'emergencia' ? 'Emergência' : ucfirst($encaminhamentoModel->urgencia),
            'observacoes' => $encaminhamentoModel->observacoes,
        ];

        $referral['paciente'] = [
            'nome' => $paciente->nome ?? '',
            'idade' => $paciente->idade ?? null,
            'cpf' => $paciente->cpf_formatado ?? '',
            'telefone' => $paciente->telefone_formatado ?? '',
        ];

        $referral['profissional'] = [
            'nome' => $profissional->nome ?? '',
            'registro_conselho' => $profissional->registro_conselho ?? null,
            'especialidade' => $profissional->especialidade->descricao ?? null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.referral-pdf', compact('referral'));

        return $pdf->stream('encaminhamento-' . $consulta->id . '.pdf');
    }

    /**
     * Imprimir receita
     */
    public function printPrescription($id)
    {
        $consulta = Consulta::with(['paciente', 'profissional'])
            ->where('id', $id)
            ->firstOrFail();

        $prescricao = \App\Models\Prescricao::where('consulta_id', $consulta->id)
            ->where('tenant_id', $consulta->tenant_id)
            ->whereNull('deleted_at')
            ->first();

        $paciente = $consulta->paciente;
        $profissional = $consulta->profissional;

        $dataReceita = $consulta->atendido_em
            ? \Carbon\Carbon::parse($consulta->atendido_em)->format('Y-m-d')
            : \Carbon\Carbon::now('America/Manaus')->format('Y-m-d');

        $prescription = [
            'numero' => 'RX-' . date('Y') . '-' . str_pad($consulta->id, 4, '0', STR_PAD_LEFT),
            'data' => $dataReceita,
            'paciente' => [
                'nome' => $paciente->nome ?? '',
                'idade' => $paciente->idade ?? null,
                'cpf' => $paciente->cpf_formatado ?? '',
                'telefone' => $paciente->telefone_formatado ?? '',
            ],
            'profissional' => [
                'nome' => $profissional->nome ?? '',
                'registro_conselho' => $profissional->registro_conselho ?? null,
                'especialidade' => $profissional->especialidade->descricao ?? null,
            ],
            'prescricao' => [
                'od_esferico' => optional($prescricao)->esfera_od,
                'od_cilindrico' => optional($prescricao)->cilindro_od,
                'od_eixo' => optional($prescricao)->eixo_od,
                'od_dnp' => optional($prescricao)->dnp_od,
                'od_altura' => optional($prescricao)->altura_od,
                'od_adicao' => optional($prescricao)->adicao_od,

                'oe_esferico' => optional($prescricao)->esfera_oe,
                'oe_cilindrico' => optional($prescricao)->cilindro_oe,
                'oe_eixo' => optional($prescricao)->eixo_oe,
                'oe_dnp' => optional($prescricao)->dnp_oe,
                'oe_altura' => optional($prescricao)->altura_oe,
                'oe_adicao' => optional($prescricao)->adicao_oe,

                'tipo_lente' => optional($prescricao)->tipo_lente,
                'validade_dias' => optional($prescricao)->validade_dias,
            ],
            'diagnostico' => optional($prescricao)->diagnostico,
            'recomendacoes' => optional($prescricao)->recomendacoes,
            'observacoes' => optional($prescricao)->observacoes,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.prescription-pdf', compact('prescription'));

        return $pdf->stream('receita-' . $consulta->id . '.pdf');
    }

    public function newPrescription()
    {
        return view('professional.new-prescription');
    }

    public function storeNewPrescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'telefone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email'],
            'od_esferico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'od_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'od_eixo' => ['nullable', 'integer', 'between:0,180'],
            'oe_esferico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'oe_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'oe_eixo' => ['nullable', 'integer', 'between:0,180'],
            'od_dnp' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'oe_dnp' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'od_altura' => ['nullable', 'string', 'max:100'],
            'oe_altura' => ['nullable', 'string', 'max:100'],
            'od_adicao' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'oe_adicao' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
            'tipo_lente' => ['nullable', 'string', 'max:100'],
            'validade_dias' => ['nullable', 'integer', 'in:30,90,180,365'],
            'diagnostico' => ['nullable', 'string', 'max:255'],
            'observacoes_receita' => ['nullable', 'string', 'max:1000'],
            'recomendacoes' => ['nullable', 'string', 'max:1000'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tenantId = AuthHelper::tenantId();
        $locationId = AuthHelper::locationId();
        $userId = AuthHelper::userId();

        $cpfLimpo = preg_replace('/\D/', '', (string) $request->input('cpf'));
        $telefoneLimpo = preg_replace('/\D/', '', (string) $request->input('telefone'));

        $pessoa = Pessoa::firstOrCreate(
            ['cpf' => $cpfLimpo, 'tenant_id' => $tenantId],
            [
                'nome' => $request->input('nome'),
                'telefone' => $telefoneLimpo,
                'email' => $request->input('email'),
                'location_id' => $locationId,
                'user_id' => $userId,
                'ativo' => true,
            ]
        );
        $pessoa->nome = $request->input('nome');
        $pessoa->telefone = $telefoneLimpo;
        $pessoa->email = $request->input('email');
        $pessoa->save();

        $profissional = Profissional::where('user_id', $userId)->first();

        $consulta = new Consulta;
        $consulta->tenant_id = $tenantId;
        $consulta->location_id = $locationId;
        $consulta->user_id = $userId;
        $consulta->pessoa_paciente_id = $pessoa->id;
        $consulta->profissional_id = $profissional ? $profissional->id : null;
        $consulta->tipo = Consulta::TIPO_CONSULTA;
        $consulta->status = Consulta::STATUS_ATENDIDO;
        $consulta->atendido_em = \Carbon\Carbon::now('America/Manaus');
        $consulta->ativo = true;
        $consulta->save();

        $data = $request->all();
        foreach (['od_esferico', 'od_cilindrico', 'oe_esferico', 'oe_cilindrico', 'od_adicao', 'oe_adicao', 'od_dnp', 'oe_dnp', 'od_altura', 'oe_altura'] as $f) {
            if (! empty($data[$f])) {
                $data[$f] = str_replace(',', '.', $data[$f]);
            }
        }

        Prescricao::create([
            'tenant_id' => $tenantId,
            'consulta_id' => $consulta->id,
            'user_id' => $userId,
            'esfera_od' => $data['od_esferico'] ?? null,
            'cilindro_od' => $data['od_cilindrico'] ?? null,
            'eixo_od' => $data['od_eixo'] ?? null,
            'esfera_oe' => $data['oe_esferico'] ?? null,
            'cilindro_oe' => $data['oe_cilindrico'] ?? null,
            'eixo_oe' => $data['oe_eixo'] ?? null,
            'dnp_od' => $data['od_dnp'] ?? null,
            'dnp_oe' => $data['oe_dnp'] ?? null,
            'altura_od' => $data['od_altura'] ?? null,
            'altura_oe' => $data['oe_altura'] ?? null,
            'adicao_od' => $data['od_adicao'] ?? null,
            'adicao_oe' => $data['oe_adicao'] ?? null,
            'tipo_lente' => $data['tipo_lente'] ?? null,
            'validade_dias' => $data['validade_dias'] ?? null,
            'diagnostico' => $data['diagnostico'] ?? null,
            'observacoes' => $data['observacoes_receita'] ?? null,
            'recomendacoes' => $data['recomendacoes'] ?? null,
            'ativo' => true,
            'location_id' => $locationId,
            'pessoa_paciente_id' => $pessoa->id,
        ]);

        return redirect()->route('professional.index')->with('success', 'Receita criada com sucesso!');
    }

    public function togglePause(Request $request, $id)
    {

        $profissional = Profissional::findOrFail($id);
        $profissional->pausar_atendimento = ! $profissional->pausar_atendimento;
        $profissional->save();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->status = $request->input('status');
        $consulta->save();

        return response()->json(['success' => true]);
    }

    public function savePrescriptionDraft(Request $request, $id)
    {
        try {
            $requestData = $request->all();

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
            foreach ($decimalFields as $f) {
                if ($request->filled($f)) {
                    $requestData[$f] = str_replace(',', '.', $request->input($f));
                }
            }

            $rules = [
                'od_esferico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'od_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'od_eixo' => ['nullable', 'integer', 'between:0,180'],
                'oe_esferico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'oe_cilindrico' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'oe_eixo' => ['nullable', 'integer', 'between:0,180'],
                'od_dnp' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'oe_dnp' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'od_adicao' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'oe_adicao' => ['nullable', 'regex:/^[+-]?\d+(\.\d{1,2})?$/'],
                'od_altura' => ['nullable', 'string', 'max:100'],
                'oe_altura' => ['nullable', 'string', 'max:100'],
                'tipo_lente' => ['nullable', 'string', 'max:100'],
                'validade_dias' => ['nullable', 'integer', 'in:30,90,180,365'],
                'diagnostico' => ['nullable', 'string', 'max:255'],
                'observacoes_receita' => ['nullable', 'string', 'max:1000'],
                'recomendacoes' => ['nullable', 'string', 'max:1000'],
            ];

            $messages = [
                'regex' => 'O campo :attribute deve ser um número com até 2 casas decimais.',
                'integer' => 'O campo :attribute deve ser um número inteiro.',
                'between' => 'O campo :attribute deve estar entre :min e :max.',
                'numeric' => 'O campo :attribute deve ser numérico.',
                'in' => 'O campo :attribute possui um valor inválido.',
                'max' => 'O campo :attribute não pode exceder :max caracteres.',
            ];

            $validator = Validator::make($requestData, $rules, $messages);
            if ($validator->fails()) {
                return redirect()
                    ->route('professional.consultation', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput()
                    ->with('active_tab', 'prescription');
            }
            $consulta = Consulta::with(['paciente', 'profissional'])
                ->where('id', $id)
                ->firstOrFail();

            $paciente = $consulta->paciente;
            $prescricao = \App\Models\Prescricao::updateOrCreate(
                [
                    'consulta_id' => $id,
                    'tenant_id' => AuthHelper::tenantId(),
                ],
                [
                    'user_id' => AuthHelper::userId(),
                    'esfera_od' => $requestData['od_esferico'] ?? null,
                    'cilindro_od' => $requestData['od_cilindrico'] ?? null,
                    'eixo_od' => $requestData['od_eixo'] ?? null,
                    'esfera_oe' => $requestData['oe_esferico'] ?? null,
                    'cilindro_oe' => $requestData['oe_cilindrico'] ?? null,
                    'eixo_oe' => $requestData['oe_eixo'] ?? null,
                    'dnp_od' => $requestData['od_dnp'] ?? null,
                    'dnp_oe' => $requestData['oe_dnp'] ?? null,
                    'altura_od' => $requestData['od_altura'] ?? null,
                    'altura_oe' => $requestData['oe_altura'] ?? null,
                    'adicao_od' => $requestData['od_adicao'] ?? null,
                    'adicao_oe' => $requestData['oe_adicao'] ?? null,
                    'tipo_lente' => $requestData['tipo_lente'] ?? null,
                    'validade_dias' => $requestData['validade_dias'] ?? null,
                    'diagnostico' => $requestData['diagnostico'] ?? null,
                    'observacoes' => $requestData['observacoes_receita'] ?? null,
                    'recomendacoes' => $requestData['recomendacoes'] ?? null,
                    'ativo' => true,
                    'location_id' => AuthHelper::locationId(),
                    'pessoa_paciente_id' => $paciente->id,
                ]
            );

            return redirect()
                ->route('professional.consultation', ['id' => $id])
                ->with('success', 'Rascunho salvo com sucesso!')
                ->with('active_tab', 'prescription');
        } catch (\Exception $e) {
            return redirect()
                ->route('professional.consultation', ['id' => $id])
                ->with('error', 'Falha ao salvar rascunho: ' . $e->getMessage())
                ->with('active_tab', 'prescription');
        }
    }

    public function saveExame(Request $request, $id)
    {
        try {
            $rules = [
                'av_od' => ['nullable', 'string', 'max:50'],
                'av_oe' => ['nullable', 'string', 'max:50'],
                'pio_od' => ['nullable', 'integer', 'between:0,20'],
                'pio_oe' => ['nullable', 'integer', 'between:0,20'],
                'fundoscopia' => ['nullable', 'string', 'max:2000'],
                'anamnese' => ['nullable', 'string', 'max:2000'],
                'observacoes' => ['nullable', 'string', 'max:2000'],
            ];

            $messages = [
                'string' => 'O campo :attribute deve ser um texto válido.',
                'integer' => 'O campo :attribute deve ser um número inteiro.',
                'max' => 'O campo :attribute não pode exceder :max caracteres.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()
                    ->route('professional.consultation', ['id' => $id])
                    ->withErrors($validator)
                    ->withInput()
                    ->with('active_tab', 'examination');
            }
            $consulta = Consulta::with(['paciente', 'profissional'])
                ->where('id', $id)
                ->firstOrFail();

            $exame = \App\Models\Exame::updateOrCreate(
                [
                    'consulta_id' => $id,
                    'tenant_id' => AuthHelper::tenantId(),
                ],
                [
                    'user_id' => AuthHelper::userId(),
                    'anamnese' => $request->input('anamnese'),
                    'acuidade_od' => $request->input('av_od'),
                    'acuidade_oe' => $request->input('av_oe'),
                    'pressao_od' => $request->input('pio_od'),
                    'pressao_oe' => $request->input('pio_oe'),
                    'fundoscopia' => $request->input('fundoscopia'),
                    'anamnese' => $request->input('anamnese'),
                    'observacoes' => $request->input('observacoes'),
                    'ativo' => true,
                    'location_id' => AuthHelper::locationId(),
                ]
            );

            return redirect()
                ->route('professional.consultation', ['id' => $id])
                ->with('success', 'Exame salvo com sucesso!')
                ->with('active_tab', 'examination');
        } catch (\Exception $e) {
            return redirect()
                ->route('professional.consultation', ['id' => $id])
                ->with('error', 'Falha ao salvar exame: ' . $e->getMessage())
                ->with('active_tab', 'examination');
        }
    }
}
