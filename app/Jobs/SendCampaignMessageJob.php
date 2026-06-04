<?php

namespace App\Jobs;

use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendCampaignMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 30;

    public function __construct(public int $logId) {}

    public function handle(): void
    {
        $log = WhatsappCampaignLog::with('campaign.template')->find($this->logId);

        if (!$log || $log->status === 'sent') return;

        $campaign = $log->campaign;
        $template = $campaign?->template;

        if (!$campaign || !$template) {
            $this->markFailed($log, 'Campaign or template not found.');
            return;
        }

        $token   = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) {
            $this->markFailed($log, 'WhatsApp API credentials not configured.');
            return;
        }

        // ── Build API payload based on message_type ───────────────────
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $log->phone,
        ];

        $type = $template->message_type;

        if ($type === 'text') {
            $payload['type'] = 'text';
            $payload['text'] = ['body' => $template->message_body ?? ''];
        } elseif ($type === 'image') {
            $payload['type']  = 'image';
            $payload['image'] = ['link' => $template->image_url];
        } elseif ($type === 'both') {
            $payload['type']  = 'image';
            $payload['image'] = [
                'link'    => $template->image_url,
                'caption' => $template->message_body ?? '',
            ];
        }

        // ── Send via Meta API ─────────────────────────────────────────
        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", $payload);

            if ($response->successful()) {
                $log->update([
                    'status'          => 'sent',
                    'meta_message_id' => $response->json('messages.0.id'),
                    'sent_at'         => now(),
                    'error_message'   => null,
                ]);
                $this->updateCampaignCounts($campaign, 'sent');
            } else {
                $error = $response->json('error.message') ?? $response->body();
                $this->markFailed($log, $error);
            }
        } catch (\Throwable $e) {
            $this->markFailed($log, $e->getMessage());
        }

        // ── Mark campaign completed when all logs are processed ───────
        $remaining = WhatsappCampaignLog::where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->count();

        if ($remaining === 0) {
            $campaign->update(['status' => 'completed']);
        }
    }

    private function markFailed(WhatsappCampaignLog $log, string $error): void
    {
        $log->update([
            'status'        => 'failed',
            'error_message' => $error,
            'sent_at'       => now(),
        ]);
        $this->updateCampaignCounts($log->campaign, 'failed');
    }

    private function updateCampaignCounts(WhatsappCampaign $campaign, string $result): void
    {
        if ($result === 'sent') {
            WhatsappCampaign::where('id', $campaign->id)->increment('sent_count');
        } else {
            WhatsappCampaign::where('id', $campaign->id)->increment('failed_count');
        }
        WhatsappCampaign::where('id', $campaign->id)->decrement('pending_count');
    }
}
