@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<div class="max-w-xl">

    <div class="mb-6">
        <a href="{{ route('children.show', $child) }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← {{ $child->first_name }} {{ $child->last_name }}
        </a>
        <h2 class="text-xl font-semibold text-gray-800 mt-1">Schedule appointment</h2>
        <p class="text-sm text-gray-500 mt-1">
            An SMS reminder will be sent to the guardian 2 days before.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('appointments.store', $child) }}"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Vaccine due <span class="text-red-500">*</span>
            </label>
            <select name="vaccine_due"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Select vaccine</option>
                @foreach($upcoming as $due)
                    <option value="{{ $due['vaccine'] }} dose {{ $due['dose'] }}"
                        {{ old('vaccine_due') === $due['vaccine'].' dose '.$due['dose']
                            ? 'selected' : '' }}>
                        {{ $due['vaccine'] }} dose {{ $due['dose'] }}
                        — due {{ $due['due_date']->format('d M Y') }}
                        @if($due['overdue']) (Overdue) @endif
                    </option>
                @endforeach
                <option value="General checkup">General checkup</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Appointment date <span class="text-red-500">*</span>
            </label>
            <input type="date" name="scheduled_date"
                value="{{ old('scheduled_date', now()->addDays(1)->toDateString()) }}"
                min="{{ now()->toDateString() }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Guardian info --}}
        @php $guardian = $child->guardians->first(); @endphp
        @if($guardian)
        <div class="bg-gray-50 rounded-lg px-4 py-3 text-sm text-gray-600">
            <span class="font-medium">SMS reminder to:</span>
            {{ $guardian->first_name }} {{ $guardian->last_name }}
            · {{ $guardian->phone_number }}
        </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium
                       px-6 py-2 rounded-lg text-sm transition-colors">
                Confirm appointment
            </button>
            <a href="{{ route('children.show', $child) }}"
               class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection