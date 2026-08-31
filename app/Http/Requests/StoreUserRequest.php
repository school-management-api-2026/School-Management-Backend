<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|unique:users,phone',
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'image' => 'nullable|file|max:2048|mimes:png,webp,jpg,jpeg,svg',
            'role_id' => 'required|exists:roles,id'
        ];
    }
}
