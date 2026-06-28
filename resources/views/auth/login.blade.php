<x-guest-layout>
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Admin Login</h2>
    <p class="text-sm text-gray-500 mb-6">Sign in to manage your QR Employee system.</p>

    @if(session('status'))
        <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                          @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                          @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Remember me
            </label>
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg
                       text-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Sign In
        </button>
    </form>
</x-guest-layout>
