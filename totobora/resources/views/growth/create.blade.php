@extends('layouts.app')

@section('title', 'Record Growth')

@section('content')
<div class="max-w-xl">

    <div class="mb-6">
        <a href="{{ route('growth.chart', $child) }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← {{ $child->first_name }} {{ $child->last_name }}
        </a>
        <h2 class="text-xl font-semibold text-gray-800 mt-1">Record growth measurement</h2>
    </div>

    {{-- Child info bar --}}
    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl
                px-5 py-3 mb-6">
        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center
                    text-green-700 font-semibold text-sm">
            {{ strtoupper(substr($child->first_name,0,1)) }}{{ strtoupper(substr($child->last_name,0,1)) }}
        </div>
        <div>
            <p class="text-sm font-medium text-gray-800">
                {{ $child->first_name }} {{ $child->last_name }}
            </p>
            <p class="text-xs text-gray-500">
                {{ $child->unique_child }} · {{ $child->getAgeLabel() }} · {{ $child->gender }}
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('growth.store', $child) }}"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Date measured <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_measured"
                value="{{ old('date_measured', now()->toDateString()) }}"
                max="{{ now()->toDateString() }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Weight (kg) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="weight_kg"
                    value="{{ old('weight_kg') }}"
                    placeholder="e.g. 5.8" step="0.1" min="0.5" max="50"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Height (cm) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="height_cm"
                    value="{{ old('height_cm') }}"
                    placeholder="e.g. 61.0" step="0.1" min="20" max="130"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <p class="text-xs text-gray-400">
            WHO weight-for-age and height-for-age status will be calculated automatically.
        </p>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium
                       px-6 py-2 rounded-lg text-sm transition-colors">
                Save measurement
            </button>
            <a href="{{ route('growth.chart', $child) }}"
               class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection