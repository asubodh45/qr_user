<x-app-layout>
    <x-slot name="title">{{ $profile->name }}</x-slot>

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Users
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile card --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow p-6 text-center">
            @php $profileImg = $profile->images->firstWhere('is_profile', true); @endphp

            @if ($profileImg)
                <img src="{{ Storage::url($profileImg->path) }}" alt="{{ $profile->name }}"
                    class="w-28 h-28 rounded-full object-cover border-4 border-indigo-100 mx-auto mb-4">
            @else
                <div
                    class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-4xl mx-auto mb-4">
                    {{ strtoupper(substr($profile->name, 0, 1)) }}
                </div>
            @endif

            <h2 class="text-xl font-bold text-gray-800">{{ $profile->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $profile->email }}</p>

            <div class="mt-4 space-y-2 text-left">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    {{ $profile->phone }}
                </div>
                @if ($profile->address)
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $profile->address }}
                    </div>
                @endif
            </div>

            <div class="mt-6 flex gap-2">
                <a href="{{ route('admin.users.edit', $profile) }}"
                    class="flex-1 text-center py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.users.destroy', $profile) }}"
                    onsubmit="return confirm('Delete {{ addslashes($profile->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- QR code card --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">QR Code</h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl inline-block">
                        {!! QrCode::size(160)->generate(route('scan', $profile->qr_token)) !!}
                    </div>
                    {{-- <div class="space-y-2">
                        <p class="text-sm text-gray-600">Scan this code to access the employee profile.</p>
                        <div class="bg-gray-100 rounded-lg px-3 py-2 text-xs font-mono text-gray-700 break-all">
                            {{ route('scan', $profile->qr_token) }}
                        </div>
                        <p class="text-xs text-gray-400">Token: <code class="font-mono">{{ $profile->qr_token }}</code></p>
                    </div> --}}
                </div>
            </div>

            {{-- Photo gallery --}}
            @if ($profile->images->isNotEmpty())
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">Photos ({{ $profile->images->count() }})
                    </h3>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach ($profile->images as $image)
                            <div class="relative">
                                <img src="{{ Storage::url($image->path) }}" alt="Photo"
                                    class="w-full h-24 object-cover rounded-xl border-2
                                            {{ $image->is_profile ? 'border-indigo-500' : 'border-transparent' }}">
                                @if ($image->is_profile)
                                    <span
                                        class="absolute top-1 left-1 bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                        Profile
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
