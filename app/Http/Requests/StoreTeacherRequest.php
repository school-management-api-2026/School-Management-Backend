<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hire_date' => 'required|date',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|unique:users,phone',
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
