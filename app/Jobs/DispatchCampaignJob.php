<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $campaignId) {}

    public function handle(): void
    {
        $campaign = WhatsappCampaign::with('template')->find($this->campaignId);

        if (!$campaign || $campaign->status === 'completed') {
            return;
        }

        $campaign->update(['status' => 'running']);

        // ── Resolve whatsapp_prefix from settings ────────────────────
        $waPrefix = preg_replace('/[^0-9]/', '',
            \App\Models\Setting::where('key_name', 'whatsapp_prefix')->value('key_value') ?? '92'
        );

        // ── Build recipient query ─────────────────────────────────────
        $query = User::where('role', $campaign->target_role)
            ->whereNotNull('phone')
            ->where('phone', '!=', '');

        if ($campaign->clinic_id) {
            $query->whereHas('appointments', fn($q) => $q->where('clinic_id', $campaign->clinic_id));
        }

        if ($campaign->doctor_id) {
            $query->whereHas('appointments', fn($q) => $q->where('doctor_id', $campaign->doctor_id));
        }

        $recipients = $query->get(['id', 'name', 'phone']);

        if ($recipients->isEmpty()) {
            $campaign->update(['status' => 'completed', 'total_recipients' => 0]);
            return;
        }

        // ── Create campaign log entries ───────────────────────────────
        $delaySeconds = (int) ($campaign->message_delay ?? config('services.whatsapp.campaign_delay', 2));
        $offset       = 0;

        foreach ($recipients as $user) {
            $rawPhone = preg_replace('/[^0-9]/', '', $user->phone);
            if (str_starts_with($rawPhone, '0')) {
                $waPhone = $waPrefix . substr($rawPhone, 1);
            } elseif (str_starts_with($rawPhone, $waPrefix)) {
                $waPhone = $rawPhone;
            } else {
                $waPhone = $waPrefix . $rawPhone;
            }

            if (strlen($waPhone) < 10) continue; // skip invalid phones

            $log = WhatsappCampaignLog::create([
                'campaign_id'    => $campaign->id,
                'user_id'        => $user->id,
                'recipient_name' => $user->name,
                'phone'          => $waPhone,
                'status'         => 'pending',
            ]);

            // Dispatch individual send job with incremental delay
            SendCampaignMessageJob::dispatch($log->id)
                ->delay(now()->addSeconds($offset));

            $offset += $delaySeconds;
        }

        $total = $recipients->count();
        $campaign->update([
            'total_recipients' => $total,
            'pending_count'    => $total,
            'sent_count'       => 0,
            'failed_count'     => 0,
        ]);
    }
}
