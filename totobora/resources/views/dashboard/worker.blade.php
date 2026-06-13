@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">
            Welcome back, {{ Auth::user()->first_name }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            {{ Auth::user()->facility->name ?? 'TotoBora' }} &mdash;
            {{ now()->format('l, d M Y') }}
        </p>
    </div>

    <!-- Stat cards — placeholders, wired up in Sprint 2 -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Children</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Vaccines this month</p>
            <p class="text-3xl font-semibold text-gray-800 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Due soon</p>
            <p class="text-3xl font-semibold text-amber-500 mt-1">—</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Overdue</p>
            <p class="text-3xl font-semibold text-red-500 mt-1">—</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-gray-500 text-center py-8">
            No children registered yet —
            <a href="#" class="text-green-600 hover:underline">register the first child</a>
        </p>
    </div>
@endsection