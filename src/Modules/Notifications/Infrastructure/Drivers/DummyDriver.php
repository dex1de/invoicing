<?php

declare(strict_types=1);

namespace Modules\Notifications\Infrastructure\Drivers;

use Illuminate\Support\Facades\Http;

class DummyDriver implements DriverInterface
{
    public function send(
        string $toEmail,
        string $subject,
        string $message,
        string $reference,
    ): bool {
        $webhookUrl = route('notification.hook', [
            'action' => 'delivered',
            'reference' => $reference,
        ]);

        Http::get($webhookUrl);

        return true;
    }
}
