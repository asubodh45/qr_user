<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\UserImage;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = UserProfile::with('profileImage')->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserProfileRequest $request): RedirectResponse
    {
        $profile = UserProfile::create($request->only('name', 'phone', 'email', 'address'));

        if ($request->hasFile('images')) {
            $profileIndex = (int) $request->input('profile_image_index', 0);

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('users', 'public');

                $profile->images()->create([
                    'path'       => $path,
                    'is_profile' => $index === $profileIndex,
                ]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(UserProfile $user_profile): View
    {
        $user_profile->load('images');

        return view('users.show', ['profile' => $user_profile]);
    }

    public function edit(UserProfile $user_profile): View
    {
        $user_profile->load('images');

        return view('users.edit', ['profile' => $user_profile]);
    }

    public function update(UpdateUserProfileRequest $request, UserProfile $user_profile): RedirectResponse
    {
        $user_profile->update($request->only('name', 'phone', 'email', 'address'));

        // Delete selected images
        if ($request->filled('delete_images')) {
            $toDelete = UserImage::whereIn('id', $request->delete_images)
                ->where('user_profile_id', $user_profile->id)
                ->get();

            foreach ($toDelete as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Set profile image
        if ($request->filled('profile_image_id')) {
            $user_profile->images()->update(['is_profile' => false]);
            $user_profile->images()->where('id', $request->profile_image_id)->update(['is_profile' => true]);
        }

        // Upload new images
        if ($request->hasFile('images')) {
            // If no existing profile image, mark first new upload as profile
            $hasProfile = $user_profile->images()->where('is_profile', true)->exists();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('users', 'public');

                $user_profile->images()->create([
                    'path'       => $path,
                    'is_profile' => !$hasProfile && $index === 0,
                ]);

                $hasProfile = true;
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(UserProfile $user_profile): RedirectResponse
    {
        foreach ($user_profile->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $user_profile->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
