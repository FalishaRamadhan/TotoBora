@extends('layouts.app')

@section('title', 'Immunization History')

@section('content')

    <div class="mb-6">
        <a href="{{ route('children.show', $child) }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← {{ $child->first_name }} {{ $child->last_name }}
        </a>
        <h2 class="text-xl font-semibold text-gray-800 mt-1">Immunization history</h2>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-6">

        {{-- Upcoming doses --}}
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-medium text-gray-800">Upcoming per MOH schedule</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-5 py-3 font-medium text-gray-600">Vaccine</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Dose</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Due date</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcoming as $due)
                    <tr>
                        <td class="px-5 py-3 text-gray-800">{{ $due['vaccine'] }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $due['dose'] }}</td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $due['due_date']->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3">
                            @if($due['overdue'])
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                             bg-red-100 text-red-700">Overdue</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                             bg-blue-100 text-blue-700">Upcoming</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                            All scheduled vaccines complete.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Quick stats --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Given</p>
                <p class="text-3xl font-semibold text-gray-800 mt-1">
                    {{ $records->count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Remaining</p>
                <p class="text-3xl font-semibold text-amber-500 mt-1">
                    {{ count($upcoming) }}
                </p>
            </div>
            <a href="{{ route('immunizations.create', $child) }}"
               class="block w-full text-center bg-green-600 hover:bg-green-700
                      text-white text-sm font-medium py-2 rounded-lg transition-colors">
                + Record vaccine
            </a>
        </div>
    </div>

    {{-- Full history --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Administered vaccines</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Vaccine</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Dose</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Date given</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Next due</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $record)
                <tr>
                    <td class="px-5 py-3 font-medium text-gray-800">
                        {{ $record->vaccine_name }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $record->dose_number ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $record->date_administered->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        @if($record->next_due_date)
                            <span class="{{ $record->isOverdue()
                                ? 'text-red-600 font-medium' : '' }}">
                                {{ $record->next_due_date->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400">
                        {{ $record->notes ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-400">
                        No vaccines recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection