<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use App\Models\Reminder;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $sms;

    public function __construct()
    {
        $at = new AfricasTalking(
            config('services.africastalking.username'),
            config('services.africastalking.api_key')
        );
        $this->sms = $at->sms();
    }

    /**
     * Send an SMS and update the reminder's delivery status.
     */
    public function send(Reminder $reminder): bool
    {
        $guardian    = $reminder->guardian;
        $appointment = $reminder->appointment;
        $child       = $appointment->child;

        $message = "TotoBora reminder: {$child->first_name} {$child->last_name} "
            . "is due for {$appointment->vaccine_due} on "
            . $appointment->scheduled_date->format('d M Y')
            . ". Please visit {$child->facility->name}. Reply STOP to opt out.";

        try {
            $result = $this->sms->send([
                'to'      => $guardian->phone_number,
                'message' => $message,
                'from'    => config('services.africastalking.sender_id', 'TotoBora'),
            ]);

            $status = $result['status'] === 'success' ? 'sent' : 'failed';

            $reminder->update(['delivery_status' => $status]);

            Log::info("SMS {$status} to {$guardian->phone_number} for reminder {$reminder->reminder_id}");

            return $status === 'sent';

        } catch (\Exception $e) {
            $reminder->update(['delivery_status' => 'failed']);
            Log::error("SMS failed for reminder {$reminder->reminder_id}: " . $e->getMessage());
            return false;
        }
    }
}