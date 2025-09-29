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
            'user_id' => ['required', 'exists:users'],
            'name' => ['required'],
            'description' => ['nullable'],
            'is_active' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
