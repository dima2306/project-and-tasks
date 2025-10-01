<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:34
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['exists:users'],
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'min:5', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'აღნიშნული ავტორი არ არსებობს',
            'name.required' => ':attribute სავალდებულოა',
            'name.string' => ':attribute უნდა იყოს ტექსტი',
            'name.min' => ':attribute უნდა იყოს მინიმუმ :min სიმბოლო',
            'name.max' => ':attribute არ უნდა აღემატებოდეს :max სიმბოლოს',
            'description.string' => ':attribute უნდა იყოს ტექსტი',
            'description.min' => ':attribute უნდა იყოს მინიმუმ :min სიმბოლო',
            'description.max' => ':attribute არ უნდა აღემატებოდეს :max სიმბოლოს',
            'is_active.boolean' => ':attribute უნდა იყოს true ან false',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'სახელი',
            'description' => 'აღწერა',
            'is_active' => 'სტატუსი',
        ];
    }


}
