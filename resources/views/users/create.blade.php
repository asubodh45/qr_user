<x-app-layout>
    <x-slot name="title">Add User</x-slot>

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
        <h2 class="text-xl font-bold text-gray-800 mb-6">New Employee</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('name') border-red-500 @else border-gray-300 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required maxlength="30"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('phone') border-red-500 @else border-gray-300 @enderror">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('email') border-red-500 @else border-gray-300 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea id="address" name="address" rows="3" maxlength="500"
                          class="w-full px-4 py-2.5 border rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                 @error('address') border-red-500 @else border-gray-300 @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Images --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photos <span class="text-gray-400 font-normal">(jpg / png / jpeg — max 2 MB each)</span></label>
                <input id="images" name="images[]" type="file" multiple accept="image/jpg,image/jpeg,image/png"
                       onchange="previewImages(this)"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600
                              hover:file:bg-indigo-100 cursor-pointer">
                @error('images.*')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Preview grid --}}
                <div id="preview-grid" class="mt-3 grid grid-cols-4 gap-3 hidden"></div>
                <p id="profile-hint" class="mt-2 text-xs text-gray-400 hidden">Click an image to set it as the profile photo.</p>
                <input type="hidden" name="profile_image_index" id="profile_image_index" value="0">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Create User
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImages(input) {
            const grid = document.getElementById('preview-grid');
            const hint = document.getElementById('profile-hint');
            const indexInput = document.getElementById('profile_image_index');
            grid.innerHTML = '';

            if (!input.files.length) {
                grid.classList.add('hidden');
                hint.classList.add('hidden');
                return;
            }

            grid.classList.remove('hidden');
            hint.classList.remove('hidden');

            Array.from(input.files).forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'relative cursor-pointer group';
                    div.dataset.index = i;

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-20 object-cover rounded-lg border-2 border-transparent transition-all';

                    const badge = document.createElement('span');
                    badge.textContent = 'Profile';
                    badge.className = 'absolute inset-0 flex items-center justify-center text-xs font-bold text-white bg-indigo-600/80 rounded-lg opacity-0 transition-opacity';

                    div.appendChild(img);
                    div.appendChild(badge);
                    grid.appendChild(div);

                    if (i === 0) setProfile(div, img, badge);

                    div.addEventListener('click', () => setProfile(div, img, badge));
                };
                reader.readAsDataURL(file);
            });

            function setProfile(div, img, badge) {
                grid.querySelectorAll('img').forEach(el => el.classList.replace('border-indigo-500', 'border-transparent'));
                grid.querySelectorAll('span').forEach(el => el.classList.add('opacity-0'));
                img.classList.replace('border-transparent', 'border-indigo-500');
                badge.classList.remove('opacity-0');
                indexInput.value = div.dataset.index;
            }
        }
    </script>
</x-app-layout>
