<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\Appointment;
use App\Services\VaccineSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImmunizationController extends Controller
{
    public function create(Child $child)
    {
        $this->authorizeFacility($child);

        // Fetch upcoming vaccines from schedule
        $given = $child->immunizations
            ->map(fn($r) => [
                'vaccine_name' => $r->vaccine_name,
                'dose_number'  => $r->dose_number,
            ])->toArray();

        $upcoming = VaccineSchedule::upcomingForChild(
            Carbon::parse($child->date_of_birth),
            $given
        );

        $scheduleOptions = VaccineSchedule::schedule();

        return view('immunizations.create', compact('child', 'upcoming', 'scheduleOptions'));
    }

    public function store(Request $request, Child $child)
    {
        $this->authorizeFacility($child);

        $validated = $request->validate([
            'vaccine_name'      => ['required', 'string', 'max:100'],
            'dose_number'       => ['required', 'integer', 'min:1'],
            'date_administered' => ['required', 'date', 'before_or_equal:today'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $child) {
            // Calculate next due date from MOH schedule
            $nextDue = VaccineSchedule::nextDueDate(
                $validated['vaccine_name'],
                (int) $validated['dose_number'],
                Carbon::parse($child->date_of_birth)
            );

            // Save immunization record
            ImmunizationRecord::create([
                'child_id'          => $child->child_id,
                'vaccine_name'      => $validated['vaccine_name'],
                'dose_number'       => $validated['dose_number'],
                'date_administered' => $validated['date_administered'],
                'next_due_date'     => $nextDue,
                'worker_id'         => Auth::id(),
                'notes'             => $validated['notes'] ?? null,
            ]);

            // Auto-create appointment for next dose if due date exists
            if ($nextDue) {
                $nextVaccineLabel = VaccineSchedule::doseLabel(
                    $validated['vaccine_name'],
                    (int) $validated['dose_number'] + 1
                );

                Appointment::create([
                    'child_id'       => $child->child_id,
                    'scheduled_date' => $nextDue->toDateString(),
                    'vaccine_due'    => $nextVaccineLabel,
                    'status'         => 'scheduled',
                    'worker_id'      => Auth::id(),
                ]);
            }

            // Mark any existing appointment for this vaccine as attended
            Appointment::where('child_id', $child->child_id)
                ->where('vaccine_due', 'like', '%' . $validated['vaccine_name'] . '%')
                ->where('status', 'scheduled')
                ->update(['status' => 'attended']);
        });

        return redirect()->route('children.show', $child)
            ->with('success', 'Vaccine recorded and next appointment scheduled.');
    }

    public function history(Child $child)
    {
        $this->authorizeFacility($child);

        $records = $child->immunizations()
            ->orderBy('date_administered', 'desc')
            ->get();

        $given = $records->map(fn($r) => [
            'vaccine_name' => $r->vaccine_name,
            'dose_number'  => $r->dose_number,
        ])->toArray();

        $upcoming = VaccineSchedule::upcomingForChild(
            Carbon::parse($child->date_of_birth),
            $given
        );

        return view('immunizations.history', compact('child', 'records', 'upcoming'));
    }

    private function authorizeFacility(Child $child): void
    {
        if ($child->facility_id !== Auth::user()->facility_id
            && !Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}