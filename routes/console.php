<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendWhatsappNotificationJob;
use App\Models\WhatsappNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('whatsapp:dispatch {--limit=200}', function () {
    $limit = (int) $this->option('limit');
    if ($limit < 1) {
        $limit = 200;
    }

    $ids = WhatsappNotification::where('status', 'programado')
        ->orderBy('id')
        ->limit($limit)
        ->pluck('id')
        ->toArray();

    foreach ($ids as $id) {
        SendWhatsappNotificationJob::dispatch((int) $id);
    }

    $this->info('Jobs despachados: ' . count($ids));
})->purpose('Despacha jobs para processar notificações WhatsApp programadas');
