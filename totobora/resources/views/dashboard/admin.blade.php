@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Reports & analytics</h2>
        <p class="text-sm text-gray-500 mt-1">System administrator view</p>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Children registered</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Vaccines this month</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Missed appointments</p>
            <p class="text-3xl font-semibold text-red-500 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Reminders sent</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">—</p>
        </div>
    </div>
@endsection