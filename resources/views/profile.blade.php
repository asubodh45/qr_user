<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->name }} — Employee Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-indigo-50 min-h-screen font-sans antialiased">

<div class="max-w-lg mx-auto px-4 py-12">

    {{-- Card --}}
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- Banner --}}
        <div class="h-28 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

        {{-- Avatar --}}
        <div class="px-6 pb-6">
            <div class="-mt-14 mb-4">
                @php $profileImg = $profile->images->firstWhere('is_profile', true) ?? $profile->images->first(); @endphp

                @if($profileImg)
                    <img src="{{ Storage::url($profileImg->path) }}" alt="{{ $profile->name }}"
                         class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg">
                @else
                    <div class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center
                                text-indigo-600 font-bold text-4xl border-4 border-white shadow-lg">
                        {{ strtoupper(substr($profile->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h1 class="text-2xl font-bold text-gray-900">{{ $profile->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Employee</p>

            <div class="mt-5 space-y-3">
                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span>{{ $profile->email }}</span>
                </div>

                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <span>{{ $profile->phone }}</span>
                </div>

                @if($profile->address)
                    <div class="flex items-start gap-3 text-sm text-gray-700">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span>{{ $profile->address }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Photo gallery --}}
        @if($profile->images->count() > 1)
            <div class="border-t border-gray-100 px-6 py-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Photos</h2>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($profile->images as $image)
                        <img src="{{ Storage::url($image->path) }}" alt="Photo"
                             class="w-full h-24 object-cover rounded-xl border-2
                                    {{ $image->is_profile ? 'border-indigo-400' : 'border-transparent' }}">
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="border-t border-gray-100 px-6 py-4">
            <p class="text-xs text-gray-400 text-center">
                Accessed via QR scan &bull;
                <a href="{{ route('scanner') }}" class="text-indigo-500 hover:underline">Open Scanner</a>
            </p>
        </div>

    </div>

</div>

</body>
</html>
