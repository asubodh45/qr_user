<x-app-layout>
    <x-slot name="title">Edit — {{ $profile->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Users
        </a>
    </div>

    <div class="max-w-2xl bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Edit Employee</h2>

        <form method="POST" action="{{ route('admin.users.update', $profile) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $profile->name) }}" required maxlength="255"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('name') border-red-500 @else border-gray-300 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $profile->phone) }}" required maxlength="30"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('phone') border-red-500 @else border-gray-300 @enderror">
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $profile->email) }}" required maxlength="255"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('email') border-red-500 @else border-gray-300 @enderror">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea id="address" name="address" rows="3" maxlength="500"
                          class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                 @error('address') border-red-500 @else border-gray-300 @enderror">{{ old('address', $profile->address) }}</textarea>
                @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Existing images --}}
            @if($profile->images->isNotEmpty())
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Photos</p>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($profile->images as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image->path) }}" alt="Photo"
                                     class="w-full h-24 object-cover rounded-lg border-2
                                            {{ $image->is_profile ? 'border-indigo-500' : 'border-gray-200' }}">

                                @if($image->is_profile)
                                    <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">Profile</span>
                                @endif

                                <div class="mt-1.5 flex items-center gap-1">
                                    {{-- Set as profile --}}
                                    @unless($image->is_profile)
                                        <label class="flex items-center gap-1 text-xs text-gray-500 cursor-pointer hover:text-indigo-600">
                                            <input type="radio" name="profile_image_id" value="{{ $image->id }}"
                                                   class="text-indigo-600 focus:ring-indigo-500">
                                            Set profile
                                        </label>
                                    @endunless
                                </div>

                                {{-- Delete checkbox --}}
                                <label class="flex items-center gap-1 text-xs text-red-500 cursor-pointer mt-1 hover:text-red-700">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"
                                           class="rounded border-gray-300 text-red-500 focus:ring-red-400">
                                    Delete
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload new images --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Add More Photos <span class="text-gray-400 font-normal">(jpg / png / jpeg — max 2 MB each)</span>
                </label>
                <input name="images[]" type="file" multiple accept="image/jpg,image/jpeg,image/png"
                       onchange="previewNew(this)"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600
                              hover:file:bg-indigo-100 cursor-pointer">
                @error('images.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <div id="new-preview" class="mt-3 grid grid-cols-4 gap-3 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewNew(input) {
            const grid = document.getElementById('new-preview');
            grid.innerHTML = '';
            if (!input.files.length) { grid.classList.add('hidden'); return; }
            grid.classList.remove('hidden');
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-20 object-cover rounded-lg border border-gray-200';
                    grid.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</x-app-layout>
