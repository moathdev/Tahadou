<?php

namespace App\Jobs;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries    = 3;
    public int $timeout  = 30;
    public int $backoff  = 60;

    public function __construct(
        public readonly int $giverId,
        public readonly int $receiverId,
    ) {}

    public function handle(): void
    {
        $giver    = Participant::findOrFail($this->giverId);
        $receiver = Participant::with('group')->findOrFail($this->receiverId);

        $interestsList = collect($receiver->interests)
            ->map(fn ($key) => config("tahadou.interests.{$key}", $key))
            ->implode(', ');

        $message = $this->buildMessage($giver, $receiver, $interestsList);

        $apiUrl   = config('tahadou.whatsapp.api_url');
        $apiToken = config('tahadou.whatsapp.api_token');

        if (empty($apiUrl) || empty($apiToken)) {
            Log::warning('WhatsApp API not configured. Skipping notification.', [
                'giver_id'    => $this->giverId,
                'receiver_id' => $this->receiverId,
            ]);
            return;
        }

        $response = Http::withToken($apiToken)
            ->timeout($this->timeout)
            ->post($apiUrl, [
                'phone'   => $giver->whatsapp_number,
                'message' => $message,
            ]);

        if (! $response->successful()) {
            Log::error('WhatsApp API error', [
                'status'      => $response->status(),
                'body'        => $response->body(),
                'giver_id'    => $this->giverId,
            ]);
            $this->fail(new \RuntimeException("WhatsApp API returned {$response->status()}"));
        }
    }

    private function buildMessage(Participant $giver, Participant $receiver, string $interestsList): string
    {
        $groupName = $receiver->group->name;

        return <<<MSG
        🎁 *Tahadou — Eid Gift Exchange*

        Hello {$giver->name}! 🌙

        You are part of the *{$groupName}* gift exchange group.

        🎯 *Your person is:* {$receiver->name}

        💝 *Their gift interests:*
        {$interestsList}

        Please prepare a thoughtful gift for them before Eid! 🎊

        _This message was sent automatically. Do not reply._
        MSG;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendWhatsAppNotification job failed', [
            'giver_id'    => $this->giverId,
            'receiver_id' => $this->receiverId,
            'error'       => $exception->getMessage(),
        ]);
    }
}
