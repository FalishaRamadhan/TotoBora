<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Appointment;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    // SMS reminder panel — Screen 4
    public function index()
    {
        $facilityId = Auth::user()->facility_id;

        // Upcoming scheduled reminders for this facility
        $upcoming = Reminder::with(['guardian', 'appointment.child'])
            ->whereHas('appointment.child', fn($q) =>
                $q->where('facility_id', $facilityId))
            ->where('delivery_status', 'pending')
            ->orderBy('send_datetime')
            ->get();

        // Recent log — last 20 sent/failed
        $log = Reminder::with(['guardian', 'appointment.child'])
            ->whereHas('appointment.child', fn($q) =>
                $q->where('facility_id', $facilityId))
            ->whereIn('delivery_status', ['sent', 'failed'])
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('reminders.index', compact('upcoming', 'log'));
    }

    // Manually trigger dispatch for this facility
    public function dispatch(Request $request, SmsService $sms)
    {
        $facilityId = Auth::user()->facility_id;

        $due = Reminder::with(['guardian', 'appointment.child.facility'])
            ->whereHas('appointment.child', fn($q) =>
                $q->where('facility_id', $facilityId))
            ->where('delivery_status', 'pending')
            ->where('send_datetime', '<=', Carbon::now())
            ->get();

        $sent   = 0;
        $failed = 0;

        foreach ($due as $reminder) {
            $sms->send($reminder) ? $sent++ : $failed++;
        }

        $message = $due->isEmpty()
            ? 'No reminders are due right now.'
            : "Dispatched {$due->count()} reminder(s): {$sent} sent, {$failed} failed.";

        return back()->with('success', $message);
    }
}