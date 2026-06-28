<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('user_profile')?->id;

        return [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:30', "unique:user_profiles,phone,{$profileId}"],
            'email'   => ['required', 'email', 'max:255', "unique:user_profiles,email,{$profileId}"],
            'address' => ['nullable', 'string', 'max:500'],
            'images'  => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'profile_image_id' => ['nullable', 'integer', 'exists:user_images,id'],
            'delete_images'    => ['nullable', 'array'],
            'delete_images.*'  => ['integer', 'exists:user_images,id'],
        ];
    }
}
