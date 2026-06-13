@extends('layouts.app')

@section('title', 'SMS Reminders')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">SMS reminders</h2>
        <form method="POST" action="{{ route('reminders.dispatch') }}">
            @csrf
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium
                       px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                🔔 Dispatch due now
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm
                    rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Upcoming pending reminders --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Pending reminders</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Child name</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Guardian phone</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Vaccine due</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Scheduled date</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Send on</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($upcoming as $reminder)
                    @php $badge = $reminder->statusBadge(); @endphp
                    <tr>
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $reminder->appointment->child->first_name }}
                            {{ $reminder->appointment->child->last_name }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $reminder->guardian->phone_number }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $reminder->appointment->vaccine_due }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $reminder->appointment->scheduled_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $reminder->send_datetime->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            No pending reminders.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent reminder log --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Recent reminder log</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Sent at</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Child</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Vaccine</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($log as $entry)
                    @php $badge = $entry->statusBadge(); @endphp
                    <tr>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $entry->updated_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-3 text-gray-800">
                            {{ $entry->appointment->child->first_name }}
                            {{ $entry->appointment->child->last_name }}
                            — {{ $entry->guardian->phone_number }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $entry->appointment->vaccine_due }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                            No reminders sent yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection