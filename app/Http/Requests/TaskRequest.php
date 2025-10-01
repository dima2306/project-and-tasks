<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:40
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['required', 'string', 'min:5', 'max:255'],
            'status' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function messages(): array
    {
        return [
            'project_id.required' => ':attribute აუცილებელია',
            'project_id.exists' => 'გთხოვთ აირჩიოთ :attribute',
            'title.required' => ':attribute აუცილებელია',
            'title.string' => ':attribute უნდა იყოს ტექსტი',
            'title.min' => ':attribute უნდა იყოს მინიმუმ :min სიმბოლო',
            'title.max' => ':attribute უნდა იყოს მაქსიმუმ :max სიმბოლო',
            'description.required' => ':attribute აუცილებელია',
            'description.string' => ':attribute უნდა იყოს ტექსტი',
            'description.min' => ':attribute უნდა იყოს მინიმუმ :min სიმბოლო',
            'description.max' => ':attribute უნდა იყოს მაქსიმუმ :max სიმბოლო',
            'status.required' => ':attribute აუცილებელია',
            'status.string' => ':attribute უნდა იყოს ტექსტი',
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'პროექტი',
            'title' => 'სახელი',
            'description' => 'აღწერა',
            'status' => 'სტატუსი',
        ];
    }
}
