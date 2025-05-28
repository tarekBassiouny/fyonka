<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('user') instanceof \App\Models\User
        ? $this->route('user')->id
        : $this->route('user');
        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id",
            'username' => "required|string|max:255|unique:users,username,$id",
            'role' => 'required|in:dashboard,api',
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }
}
