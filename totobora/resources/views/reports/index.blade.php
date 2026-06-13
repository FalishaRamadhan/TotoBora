@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Reports & analytics</h2>
        <p class="text-sm text-gray-500 mt-1">System-wide immunization coverage and performance.</p>
    </div>

    {{-- Top stats --}}
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Children registered</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">{{ $totalChildren }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Vaccines this month</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">{{ $vaccinesThisMonth }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Missed appointments</p>
            <p class="text-3xl font-semibold text-red-500 mt-1">{{ $missedAppointments }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Reminders sent</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">{{ $remindersSent }}</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-2 gap-6 mb-8">

        {{-- Coverage by vaccine --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
                Immunization coverage by vaccine type
            </h3>
            <canvas id="coverageChart" height="200"></canvas>
        </div>

        {{-- Monthly trend --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
                Vaccines administered — last 6 months
            </h3>
            <canvas id="trendChart" height="200"></canvas>
        </div>

    </div>

    {{-- Defaulter list --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Defaulter list</h3>
            <p class="text-xs text-gray-500 mt-1">
                Children with missed appointments, sorted by days overdue.
            </p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Child name</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Missed vaccine</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Due date</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Guardian phone</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Days overdue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($defaulters as $d)
                    <tr class="hover:bg-gray-50 cursor-pointer"
                        onclick="window.location='{{ route('children.show', $d['child']) }}'">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $d['child']->first_name }} {{ $d['child']->last_name }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $d['vaccine'] }}</td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($d['due_date'])->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $d['guardian']?->phone_number ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-semibold text-red-600">
                                {{ $d['days_overdue'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            No defaulters — all appointments up to date.
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
    // Coverage by vaccine bar chart
    new Chart(document.getElementById('coverageChart'), {
        type: 'bar',
        data: {
            labels: @json($vaccines),
            datasets: [{
                label: 'Children covered',
                data: @json($coverage),
                backgroundColor: '#16a34a',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    title: { display: true, text: 'Children', font: { size: 11 } }
                }
            }
        }
    });

    // Monthly trend line chart
    const trend = @json($trend);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trend.map(t => t.label),
            datasets: [{
                label: 'Vaccines given',
                data: trend.map(t => t.total),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563eb',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    title: { display: true, text: 'Count', font: { size: 11 } }
                }
            }
        }
    });
</script>
@endsection