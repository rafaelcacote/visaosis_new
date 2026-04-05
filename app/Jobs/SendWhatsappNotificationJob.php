<?php

namespace App\Jobs;

use App\Models\WhatsappNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $notificationId) {}

    public function handle(): void
    {
        $notification = WhatsappNotification::where('id', $this->notificationId)->first();
        if (! $notification) {
            return;
        }

        if ($notification->status !== 'programado') {
            return;
        }

        $notification->tentativas = ((int) ($notification->tentativas ?? 0)) + 1;
        $notification->status = 'processando';
        $notification->erro = null;
        $notification->save();

        if (! $notification->telefone) {
            $notification->status = 'falhou';
            $notification->erro = 'Telefone do cliente não informado.';
            $notification->save();

            return;
        }

        $waPhone = $this->normalizeWhatsappPhone($notification->telefone);
        if (! $waPhone) {
            $notification->status = 'falhou';
            $notification->erro = 'Telefone inválido.';
            $notification->save();

            return;
        }

        $mensagem = (string) ($notification->mensagem ?? '');
        if ($mensagem === '') {
            $notification->status = 'falhou';
            $notification->erro = 'Mensagem vazia.';
            $notification->save();

            return;
        }

        $notification->wa_url = 'https://wa.me/' . $waPhone . '?text=' . urlencode($mensagem);
        $notification->telefone = $waPhone;
        $notification->status = 'enviado';
        $notification->enviado_em = now();
        $notification->save();
    }

    private function normalizeWhatsappPhone(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);
        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '55')) {
            return $digits;
        }

        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return '55' . $digits;
        }

        return $digits;
    }
}
