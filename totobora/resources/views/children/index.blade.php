@extends('layouts.app')

@section('title', 'Children')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Immunization records</h2>
        <a href="{{ route('children.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium
                  px-4 py-2 rounded-lg transition-colors">
            + Register child
        </a>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm
                    rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('children.index') }}" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search child..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72
                   focus:outline-none focus:ring-2 focus:ring-green-500">
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Child name</th>
                    <th class="px-5 py-3 font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Age</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Last vaccine</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Next due</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($children as $child)
                    @php
                        $lastVaccine  = $child->immunizations->first();
                        $nextAppt     = $child->appointments->first();
                        $today        = now()->startOfDay();

                        if ($nextAppt) {
                            $due  = \Carbon\Carbon::parse($nextAppt->scheduled_date);
                            $diff = $today->diffInDays($due, false);

                            if ($diff < 0) {
                                $status = 'Overdue';
                                $badge  = 'bg-red-100 text-red-700';
                            } elseif ($diff <= 7) {
                                $status = 'Due soon';
                                $badge  = 'bg-amber-100 text-amber-700';
                            } else {
                                $status = 'Up to date';
                                $badge  = 'bg-green-100 text-green-700';
                            }
                        } else {
                            $status = $lastVaccine ? 'Up to date' : 'No records';
                            $badge  = $lastVaccine ? 'bg-green-100 text-green-700'
                                                   : 'bg-gray-100 text-gray-500';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 cursor-pointer"
                        onclick="window.location='{{ route('children.show', $child) }}'">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $child->first_name }} {{ $child->last_name }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 font-mono text-xs">
                            {{ $child->unique_child }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $child->getAgeLabel() }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $lastVaccine ? $lastVaccine->vaccine_name : '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $nextAppt ? \Carbon\Carbon::parse($nextAppt->scheduled_date)->format('d M Y') : '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                            No children registered yet.
                            <a href="{{ route('children.create') }}"
                               class="text-green-600 hover:underline ml-1">
                                Register the first child →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($children->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $children->links() }}
            </div>
        @endif
    </div>

@endsection