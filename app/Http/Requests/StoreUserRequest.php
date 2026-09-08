<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 1. ប្រសិនបើជាការ Update (PUT/PATCH)
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $userId = null;
            if ($this->route('student')) {
                $student = $this->route('student');
                $userId = is_object($student) ? $student->user_id : $student;
            } elseif ($this->route('teacher')) {
                $teacher = $this->route('teacher');
                $userId = is_object($teacher) ? $teacher->user_id : $teacher;
            } elseif ($this->route('user')) {
                $user = $this->route('user');
                $userId = is_object($user) ? $user->id : $user;
            }

            return [
                'code' => 'nullable|string|max:255',
                'name' => 'sometimes|required|string|max:255',
                'username' => ['nullable', 'string', 'max:150', Rule::unique('users', 'username')->ignore($userId)],
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'password' => 'nullable|string|min:6',
                'phone' => ['nullable', 'string', Rule::unique('users', 'phone')->ignore($userId)],
                'gender' => 'nullable|string|max:30',
                'date_of_birth' => 'nullable|date',
                'image' => 'nullable|string',
                'role_id' => 'sometimes|required|exists:roles,id',
                
            ];
        }

        // 2. ប្រសិនបើជាការ Store (POST - បង្កើតថ្មី)
        return [
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|unique:users,phone',
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'image' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}