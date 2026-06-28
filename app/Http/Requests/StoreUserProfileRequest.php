<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:30', 'unique:user_profiles,phone'],
            'email'   => ['required', 'email', 'max:255', 'unique:user_profiles,email'],
            'address' => ['nullable', 'string', 'max:500'],
            'images'  => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'profile_image_index' => ['nullable', 'integer'],
        ];
    }
}
