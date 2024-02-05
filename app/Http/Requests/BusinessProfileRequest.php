<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $businessId = $this->business;

        $rules = [
            'nama_bisnis' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('businesses')->ignore($businessId)
            ],
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone_number' => 'required|numeric',
            'pic_business' => 'required|string',
            'bank_account_name' => 'required|string',
            'bank_branch' => 'required|string',
            'bank_account_number' => 'required|numeric',
        ];

        if ($this->isMethod('post')) {
            // For store operation, enforce unique check without ignoring any business
            $rules['email'] = 'required|string|email|max:255|unique:businesses';
            $rules['full_address'] = 'required|string|max:100000';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nama_bisnis.required' => 'The business name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'business_logo.image' => 'The business logo must be an image.',
            'business_logo.mimes' => 'The business logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'business_logo.max' => 'The business logo may not be greater than 2048 kilobytes.',
        ];
    }
}
