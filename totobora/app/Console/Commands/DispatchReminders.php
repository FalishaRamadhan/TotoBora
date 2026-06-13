<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class DispatchReminders extends Command
{
    protected $signature   = 'reminders:dispatch';
    protected $description = 'Dispatch pending SMS reminders for upcoming appointments';

    public function handle(SmsService $sms): int
    {
        // Fetch all pending reminders whose send_datetime has passed
        $due = Reminder::with(['guardian', 'appointment.child.facility'])
            ->where('delivery_status', 'pending')
            ->where('send_datetime', '<=', Carbon::now())
            ->get();

        if ($due->isEmpty()) {
            $this->info('No pending reminders due.');
            return Command::SUCCESS;
        }

        $this->info("Dispatching {$due->count()} reminder(s)...");

        $sent   = 0;
        $failed = 0;

        foreach ($due as $reminder) {
            $result = $sms->send($reminder);
            $result ? $sent++ : $failed++;
            $this->line(
                ($result ? '  ✓' : '  ✗')
                . " Reminder #{$reminder->reminder_id}"
                . " → {$reminder->guardian->phone_number}"
            );
        }

        $this->info("Done. Sent: {$sent} | Failed: {$failed}");

        return Command::SUCCESS;
    }
}