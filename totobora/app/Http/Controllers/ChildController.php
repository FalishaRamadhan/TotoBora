<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    // Immunization dashboard — Screen 2
    public function index(Request $request)
    {
        $query = Child::with(['guardians', 'immunizations' => function ($q) {
                $q->latest('date_administered');
            }, 'appointments' => function ($q) {
                $q->where('status', 'scheduled')->orderBy('scheduled_date');
            }])
            ->where('facility_id', Auth::user()->facility_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('unique_child', 'like', "%{$search}%");
            });
        }

        $children = $query->latest()->paginate(15)->withQueryString();

        return view('children.index', compact('children'));
    }

    // Child registration form — Screen 1
    public function create()
    {
        return view('children.create');
    }

    // Store child + guardian in one transaction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => ['required', 'string', 'max:50'],
            'last_name'         => ['required', 'string', 'max:50'],
            'date_of_birth'     => ['required', 'date', 'before:today'],
            'gender'            => ['required', 'in:Male,Female'],
            'birth_weight'      => ['nullable', 'numeric', 'min:0.5', 'max:10'],
            'guardian_first_name' => ['required', 'string', 'max:50'],
            'guardian_last_name'  => ['required', 'string', 'max:50'],
            'phone_number'      => ['required', 'string', 'max:15', 'unique:guardians,phone_number'],
            'email'             => ['nullable', 'email', 'max:100'],
            'relationship'      => ['required', 'in:Mother,Father,Grandparent,Aunt/Uncle,Sibling,Other'],
        ]);

        DB::transaction(function () use ($validated) {
            $uniqueKey = strtolower($validated['first_name'])
                . strtolower($validated['last_name'])
                . $validated['date_of_birth']
                . Auth::user()->facility_id;

            $child = Child::create([
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'date_of_birth'  => $validated['date_of_birth'],
                'gender'         => $validated['gender'],
                'birth_weight'   => $validated['birth_weight'] ?? null,
                'facility_id'    => Auth::user()->facility_id,
                'unique_child'   => md5($uniqueKey),
            ]);

            // Generate CH-XXXXX ID stored as a readable reference
            $child->update([
                'unique_child' => 'CH-' . str_pad($child->child_id, 5, '0', STR_PAD_LEFT),
            ]);

            Guardian::create([
                'child_id'     => $child->child_id,
                'first_name'   => $validated['guardian_first_name'],
                'last_name'    => $validated['guardian_last_name'],
                'phone_number' => $validated['phone_number'],
                'email'        => $validated['email'] ?? null,
                'relationship' => $validated['relationship'],
            ]);
        });

        return redirect()->route('children.index')
            ->with('success', 'Child registered successfully.');
    }

    // Child profile page
    public function show(Child $child)
    {
        $this->authorizeFacility($child);

        $child->load([
            'guardians',
            'immunizations' => fn($q) => $q->orderBy('date_administered', 'desc'),
            'appointments'  => fn($q) => $q->orderBy('scheduled_date'),
            'growthMeasurements' => fn($q) => $q->orderBy('date_measured'),
        ]);

        return view('children.show', compact('child'));
    }

    public function edit(Child $child)
    {
        $this->authorizeFacility($child);
        return view('children.edit', compact('child'));
    }

    public function update(Request $request, Child $child)
    {
        $this->authorizeFacility($child);

        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:Male,Female'],
            'birth_weight'  => ['nullable', 'numeric', 'min:0.5', 'max:10'],
        ]);

        $child->update($validated);

        return redirect()->route('children.show', $child)
            ->with('success', 'Child record updated.');
    }

    // Prevent workers from accessing other facilities' children
    private function authorizeFacility(Child $child): void
    {
        if ($child->facility_id !== Auth::user()->facility_id && !Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}