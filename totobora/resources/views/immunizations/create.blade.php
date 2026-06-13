@extends('layouts.app')

@section('title', 'Record Vaccine')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <a href="{{ route('children.show', $child) }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← {{ $child->first_name }} {{ $child->last_name }}
        </a>
        <h2 class="text-xl font-semibold text-gray-800 mt-1">Record vaccination</h2>
    </div>

    {{-- Upcoming vaccines from MOH schedule --}}
    @if(count($upcoming))
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-3">
            Due per MOH schedule
        </p>
        <div class="space-y-2">
            @foreach($upcoming as $due)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-800">
                    {{ $due['vaccine'] }} dose {{ $due['dose'] }}
                </span>
                <span class="{{ $due['overdue'] ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                    {{ $due['due_date']->format('d M Y') }}
                    @if($due['overdue']) · Overdue @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('immunizations.store', $child) }}"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Vaccine <span class="text-red-500">*</span>
                </label>
                <select name="vaccine_name" id="vaccine_name"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select vaccine</option>
                    @foreach(array_keys($scheduleOptions) as $vaccine)
                        <option value="{{ $vaccine }}"
                            {{ old('vaccine_name') === $vaccine ? 'selected' : '' }}>
                            {{ $vaccine }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Dose number <span class="text-red-500">*</span>
                </label>
                <select name="dose_number" id="dose_number"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select dose</option>
                    @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}"
                            {{ old('dose_number') == $i ? 'selected' : '' }}>
                            Dose {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date administered <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date_administered"
                    value="{{ old('date_administered', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                placeholder="Any observations or reactions..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium
                       px-6 py-2 rounded-lg text-sm transition-colors">
                Save vaccination
            </button>
            <a href="{{ route('children.show', $child) }}"
               class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection