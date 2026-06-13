@extends('layouts.app')

@section('title', 'Register Child')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Register a child</h2>
        <p class="text-sm text-gray-500 mt-1">Fill in the child and guardian details below.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('children.store') }}" class="space-y-8">
        @csrf

        {{-- Child details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                Child details
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        First name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                        placeholder="e.g. Amara"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Last name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                        placeholder="e.g. Otieno"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date of birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                        max="{{ now()->subDay()->toDateString() }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select</option>
                        <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Birth weight (kg)
                    </label>
                    <input type="number" name="birth_weight" value="{{ old('birth_weight') }}"
                        placeholder="e.g. 3.2" step="0.1" min="0.5" max="10"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        {{-- Guardian details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                Guardian details
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        First name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="guardian_first_name"
                        value="{{ old('guardian_first_name') }}"
                        placeholder="Full name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Last name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="guardian_last_name"
                        value="{{ old('guardian_last_name') }}"
                        placeholder="Full name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Phone number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                        placeholder="+254 7XX XXX XXX"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Relationship <span class="text-red-500">*</span>
                    </label>
                    <select name="relationship"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select</option>
                        @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Other'] as $rel)
                            <option value="{{ $rel }}"
                                {{ old('relationship') === $rel ? 'selected' : '' }}>
                                {{ $rel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email (optional)
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="email@example.com"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        {{-- Facility (auto-filled) --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                Facility
            </h3>
            <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200
                        rounded-lg text-sm text-gray-600">
                <span>🏥</span>
                <span>{{ Auth::user()->facility->name ?? 'Unknown facility' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2
                       rounded-lg text-sm transition-colors">
                Register child
            </button>
            <a href="{{ route('children.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection