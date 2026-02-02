<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|min:3',
            'description' => 'sometimes|nullable|string',
            'completed' => 'sometimes|boolean',
        ];
    }
}

