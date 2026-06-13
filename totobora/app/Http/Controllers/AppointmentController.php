<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Appointment;
use App\Models\Reminder;
use App\Services\VaccineSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function create(Child $child)
    {
        $this->authorizeFacility($child);

        $given = $child->immunizations
            ->map(fn($r) => [
                'vaccine_name' => $r->vaccine_name,
                'dose_number'  => $r->dose_number,
            ])->toArray();

        $upcoming = VaccineSchedule::upcomingForChild(
            Carbon::parse($child->date_of_birth),
            $given
        );

        return view('appointments.create', compact('child', 'upcoming'));
    }

    public function store(Request $request, Child $child)
    {
        $this->authorizeFacility($child);

        $validated = $request->validate([
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'vaccine_due'    => ['required', 'string', 'max:100'],
        ]);

        $appointment = Appointment::create([
            'child_id'       => $child->child_id,
            'scheduled_date' => $validated['scheduled_date'],
            'vaccine_due'    => $validated['vaccine_due'],
            'status'         => 'scheduled',
            'worker_id'      => Auth::id(),
        ]);

        // Auto-create a pending SMS reminder for the guardian
        $guardian = $child->guardians->first();
        if ($guardian) {
            Reminder::create([
                'appointment_id'  => $appointment->appointment_id,
                'guardian_id'     => $guardian->guardian_id,
                'send_datetime'   => Carbon::parse($validated['scheduled_date'])
                                        ->subDays(2)->setTime(8, 0),
                'channel'         => 'SMS',
                'delivery_status' => 'pending',
            ]);
        }

        return redirect()->route('children.show', $child)
            ->with('success', 'Appointment scheduled and reminder queued.');
    }

    public function attend(Appointment $appointment)
    {
        $appointment->update(['status' => 'attended']);

        return back()->with('success', 'Appointment marked as attended.');
    }

    public function miss(Appointment $appointment)
    {
        $appointment->update(['status' => 'missed']);

        return back()->with('success', 'Appointment marked as missed.');
    }

    private function authorizeFacility(Child $child): void
    {
        if ($child->facility_id !== Auth::user()->facility_id
            && !Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}