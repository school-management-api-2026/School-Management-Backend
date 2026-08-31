<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher');
        $userId = $teacherId ? $teacherId->user_id : null;

        return [
            'hire_date' => 'sometimes|date',
            'name' => 'sometimes|required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username,' . $userId,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|unique:users,phone,' . $userId,
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'role_id' => 'sometimes|required|exists:roles,id',
        ];
    }
}
