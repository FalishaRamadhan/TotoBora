<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GrowthMeasurement;
use App\Services\WHOReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GrowthController extends Controller
{
    public function create(Child $child)
    {
        $this->authorizeFacility($child);
        return view('growth.create', compact('child'));
    }

    public function store(Request $request, Child $child)
    {
        $this->authorizeFacility($child);

        $validated = $request->validate([
            'date_measured' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg'     => ['required', 'numeric', 'min:0.5', 'max:50'],
            'height_cm'     => ['required', 'numeric', 'min:20',  'max:130'],
        ]);

        // Calculate age in months at time of measurement
        $ageAtMeasurement = (int) Carbon::parse($child->date_of_birth)
            ->diffInMonths(Carbon::parse($validated['date_measured']));

        $weightStatus = WHOReference::classifyWeight(
            (float) $validated['weight_kg'],
            $ageAtMeasurement,
            $child->gender
        );

        $heightStatus = WHOReference::classifyHeight(
            (float) $validated['height_cm'],
            $ageAtMeasurement,
            $child->gender
        );

        GrowthMeasurement::create([
            'child_id'          => $child->child_id,
            'date_measured'     => $validated['date_measured'],
            'weight_kg'         => $validated['weight_kg'],
            'height_cm'         => $validated['height_cm'],
            'who_weight_status' => $weightStatus,
            'who_height_status' => $heightStatus,
            'worker_id'         => Auth::id(),
        ]);

        return redirect()->route('growth.chart', $child)
            ->with('success', 'Growth measurement recorded.');
    }

    public function chart(Child $child)
    {
        $this->authorizeFacility($child);

        $measurements = $child->growthMeasurements()
            ->orderBy('date_measured')
            ->get();

        // Build child's actual data points
        $weightPoints = $measurements->map(fn($m) => [
            'x' => (int) Carbon::parse($child->date_of_birth)
                       ->diffInMonths($m->date_measured),
            'y' => (float) $m->weight_kg,
        ])->values()->toArray();

        $heightPoints = $measurements->map(fn($m) => [
            'x' => (int) Carbon::parse($child->date_of_birth)
                       ->diffInMonths($m->date_measured),
            'y' => (float) $m->height_cm,
        ])->values()->toArray();

        // WHO reference bands
        $weightBands = WHOReference::chartBands($child->gender, 'weight');
        $heightBands = WHOReference::chartBands($child->gender, 'height');

        // Latest measurement for WHO status display
        $latest = $measurements->last();

        return view('growth.chart', compact(
            'child',
            'measurements',
            'weightPoints',
            'heightPoints',
            'weightBands',
            'heightBands',
            'latest'
        ));
    }

    private function authorizeFacility(Child $child): void
    {
        if ($child->facility_id !== Auth::user()->facility_id
            && !Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}