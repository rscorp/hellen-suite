<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|string|hashed_exists:identification_types,id',
            'dni' => 'required|numeric|unique:guests,dni',
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'gender' => 'nullable|string|in:f,m,x',
            'birtdate' => 'nullable|date',
            'profession' => 'nullable|string',
            'nationality' => 'required|string|hashed_exists:countries,id'
        ];
    }
}
