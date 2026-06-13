<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\Appointment;
use App\Models\Reminder;
use App\Services\VaccineSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Top-level stats
        $totalChildren    = Child::count();
        $vaccinesThisMonth = ImmunizationRecord::whereMonth('date_administered', now()->month)
            ->whereYear('date_administered', now()->year)
            ->count();
        $missedAppointments = Appointment::where('status', 'missed')->count();
        $remindersSent      = Reminder::where('delivery_status', 'sent')->count();

        // Coverage by vaccine — count of children who received at least dose 1
        $coverageRaw = ImmunizationRecord::select('vaccine_name', DB::raw('count(distinct child_id) as total'))
            ->where('dose_number', 1)
            ->groupBy('vaccine_name')
            ->orderBy('total', 'desc')
            ->get();

        $vaccines  = $coverageRaw->pluck('vaccine_name');
        $coverage  = $coverageRaw->pluck('total');
        $coveragePct = $coverageRaw->map(fn($r) =>
            $totalChildren > 0 ? round(($r->total / $totalChildren) * 100) : 0
        );

        // Defaulter list — children with missed appointments
        $defaulters = Child::with(['guardians', 'appointments' => fn($q) =>
                $q->where('status', 'missed')->orderBy('scheduled_date', 'desc')
            ])
            ->whereHas('appointments', fn($q) => $q->where('status', 'missed'))
            ->get()
            ->map(function ($child) {
                $missed = $child->appointments->first();
                return [
                    'child'        => $child,
                    'guardian'     => $child->guardians->first(),
                    'vaccine'      => $missed->vaccine_due,
                    'due_date'     => $missed->scheduled_date,
                    'days_overdue' => (int) Carbon::parse($missed->scheduled_date)
                                         ->diffInDays(now()),
                ];
            })
            ->sortByDesc('days_overdue');

        // Monthly trend — vaccines given per month for the last 6 months
        $trend = ImmunizationRecord::select(
                DB::raw('MONTH(date_administered) as month'),
                DB::raw('YEAR(date_administered) as year'),
                DB::raw('count(*) as total')
            )
            ->where('date_administered', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'label' => Carbon::create($r->year, $r->month)->format('M Y'),
                'total' => $r->total,
            ]);

        return view('reports.index', compact(
            'totalChildren',
            'vaccinesThisMonth',
            'missedAppointments',
            'remindersSent',
            'vaccines',
            'coverage',
            'coveragePct',
            'defaulters',
            'trend'
        ));
    }
}