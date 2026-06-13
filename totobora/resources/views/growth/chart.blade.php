@extends('layouts.app')

@section('title', 'Growth Monitoring')

@section('content')

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center
                    text-green-700 font-semibold">
            {{ strtoupper(substr($child->first_name,0,1)) }}{{ strtoupper(substr($child->last_name,0,1)) }}
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $child->first_name }} {{ $child->last_name }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ $child->unique_child }} · Age: {{ $child->getAgeLabel() }} · {{ $child->gender }}
            </p>
        </div>
        <a href="{{ route('growth.create', $child) }}"
           class="ml-auto bg-green-600 hover:bg-green-700 text-white text-sm font-medium
                  px-4 py-2 rounded-lg transition-colors">
            + Record measurement
        </a>
    </div>

    {{-- WHO status badges --}}
    @if($latest)
    <div class="flex items-center gap-3 mb-6">
        @php
            $wb = $latest->weightBadge();
            $hb = $latest->heightBadge();
        @endphp
        <span class="text-sm text-gray-600">WHO status:</span>
        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $wb['class'] }}">
            Weight-for-age: {{ $wb['label'] }}
        </span>
        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $hb['class'] }}">
            Height-for-age: {{ $hb['label'] }}
        </span>
        @if($latest->isFlagged())
            <span class="text-xs text-red-600 font-medium">⚠ Flagged for review</span>
        @endif
    </div>
    @endif

    {{-- Charts --}}
    <div class="grid grid-cols-2 gap-6 mb-6">

        {{-- Weight chart --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
                Weight-for-age &mdash; WHO reference bands
            </h3>
            <canvas id="weightChart" height="220"></canvas>
            <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-green-500"></span> Child
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-gray-400" style="border-top:2px dashed #9ca3af"></span> Median
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-red-300"></span> -2SD (threshold)
                </span>
            </div>
        </div>

        {{-- Height chart --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
                Height-for-age &mdash; WHO reference bands
            </h3>
            <canvas id="heightChart" height="220"></canvas>
            <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-blue-500"></span> Child
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-gray-400"></span> Median
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-6 h-0.5 bg-red-300"></span> -2SD (threshold)
                </span>
            </div>
        </div>
    </div>

    {{-- Measurements table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Measurement history</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Date</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Age</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Weight (kg)</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Height (cm)</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Weight status</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Height status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($measurements->sortByDesc('date_measured') as $m)
                    @php
                        $wb = $m->weightBadge();
                        $hb = $m->heightBadge();
                        $ageAtM = (int) \Carbon\Carbon::parse($child->date_of_birth)
                                      ->diffInMonths($m->date_measured);
                    @endphp
                    <tr class="{{ $m->isFlagged() ? 'bg-red-50' : '' }}">
                        <td class="px-5 py-3 text-gray-800">
                            {{ $m->date_measured->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $ageAtM }} mo
                        </td>
                        <td class="px-5 py-3 text-gray-800 font-medium">
                            {{ $m->weight_kg }}
                        </td>
                        <td class="px-5 py-3 text-gray-800 font-medium">
                            {{ $m->height_cm }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $wb['class'] }}">
                                {{ $wb['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $hb['class'] }}">
                                {{ $hb['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            No measurements recorded yet.
                            <a href="{{ route('growth.create', $child) }}"
                               class="text-green-600 hover:underline ml-1">
                                Record the first measurement →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    // Data passed from controller
    const weightPoints = @json($weightPoints);
    const heightPoints = @json($heightPoints);
    const weightBands  = @json($weightBands);
    const heightBands  = @json($heightBands);

    // Convert band arrays to x/y format for Chart.js
    function bandToPoints(labels, values) {
        return labels.map((label, i) => ({
            x: parseInt(label),
            y: values[i]
        }));
    }

    const wMedian = bandToPoints(weightBands.labels, weightBands.median);
    const wSd2    = bandToPoints(weightBands.labels, weightBands.sd2);
    const hMedian = bandToPoints(heightBands.labels, heightBands.median);
    const hSd2    = bandToPoints(heightBands.labels, heightBands.sd2);

    const chartDefaults = {
        type: 'line',
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    type: 'linear',
                    title: { display: true, text: 'Age (months)', font: { size: 11 } },
                    min: 0, max: 24,
                },
                y: {
                    title: { display: true, font: { size: 11 } },
                }
            },
            elements: { point: { radius: 4 }, line: { tension: 0.3 } }
        }
    };

    // Weight chart
    new Chart(document.getElementById('weightChart'), {
        ...chartDefaults,
        data: {
            datasets: [
                {
                    label: 'Child weight',
                    data: weightPoints,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.1)',
                    borderWidth: 2,
                    fill: false,
                },
                {
                    label: 'WHO Median',
                    data: wMedian,
                    borderColor: '#9ca3af',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                },
                {
                    label: '-2SD',
                    data: wSd2,
                    borderColor: '#fca5a5',
                    backgroundColor: 'rgba(252,165,165,0.15)',
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: '+1',
                },
            ]
        },
        options: {
            ...chartDefaults.options,
            scales: {
                ...chartDefaults.options.scales,
                y: { title: { display: true, text: 'Weight (kg)', font: { size: 11 } } }
            }
        }
    });

    // Height chart
    new Chart(document.getElementById('heightChart'), {
        ...chartDefaults,
        data: {
            datasets: [
                {
                    label: 'Child height',
                    data: heightPoints,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.1)',
                    borderWidth: 2,
                    fill: false,
                },
                {
                    label: 'WHO Median',
                    data: hMedian,
                    borderColor: '#9ca3af',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                },
                {
                    label: '-2SD',
                    data: hSd2,
                    borderColor: '#fca5a5',
                    backgroundColor: 'rgba(252,165,165,0.15)',
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: '+1',
                },
            ]
        },
        options: {
            ...chartDefaults.options,
            scales: {
                ...chartDefaults.options.scales,
                y: { title: { display: true, text: 'Height (cm)', font: { size: 11 } } }
            }
        }
    });
</script>
@endsection