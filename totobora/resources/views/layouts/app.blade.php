<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TotoBora — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Top nav -->
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xl">🌿</span>
            <span class="text-lg font-semibold text-green-700">TotoBora</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-red-600 transition">
                    Sign out
                </button>
            </form>
        </div>
    </nav>

    <!-- Sidebar + content -->
    <div class="flex min-h-[calc(100vh-57px)]">

        <!-- Sidebar -->
        <aside class="w-52 bg-white border-r border-gray-200 py-6 px-4 flex flex-col gap-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                      {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('children.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-
                        {{ request()->routeIs('children.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                🧒 Children
            </a>
            <a href="{{ route('children.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                        {{ request()->routeIs('immunizations.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                💉 Immunizations
            </a>
            <a href="#"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                        {{ request()->routeIs('growth.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                📈 Growth
            </a>
            <a href="{{ route('reminders.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                        {{ request()->routeIs('reminders.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                🔔 Reminders
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('reports.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                        {{ request()->routeIs('reports.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                📋 Reports
            </a>
            @endif
        </aside>

        <!-- Page content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>
    @yield('scripts')
</body>
</html>