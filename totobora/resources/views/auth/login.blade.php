<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TotoBora — Sign in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 w-full max-w-sm">

        <!-- Brand -->
        <div class="flex items-center gap-2 mb-8">
            <span class="text-2xl">🌿</span>
            <span class="text-xl font-semibold text-green-700">TotoBora</span>
        </div>

        <h1 class="text-gray-800 font-semibold text-lg mb-1">Sign in</h1>
        <p class="text-sm text-gray-500 mb-6">Child immunization & growth monitoring</p>

        <!-- Error -->
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email"
                    class="block text-sm font-medium text-gray-700 mb-1">
                    Email address
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="password"
                    class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" id="remember" name="remember"
                    class="rounded border-gray-300 text-green-600 mr-2">
                <label for="remember" class="text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium
                       py-2 rounded-lg text-sm transition-colors">
                Sign in
            </button>
        </form>
    </div>

</body>
</html>