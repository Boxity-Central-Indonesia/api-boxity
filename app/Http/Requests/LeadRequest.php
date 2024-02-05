<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nama_prospek' => 'required',
            'email_prospek' => 'required|email',
            'nomor_telepon_prospek' => 'nullable',
            'tipe_prospek' => 'required|in:perorangan,bisnis,rekomendasi',
        ];
        if ($this->isMethod('post')) {
            $rules['email_prospek'] .= '|unique:leads';
        }
        if ($this->isMethod('put')) {
            $rules['email_prospek'] .= '|unique:leads,email_prospek,' . $this->lead;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nama_prospek.required' => 'Nama prospek harus diisi.',
            'email_prospek.required' => 'Email prospek harus diisi.',
            'email_prospek.email' => 'Email prospek harus valid.',
            'email_prospek.unique' => 'Email prospek sudah terdaftar.',
            'tipe_prospek.required' => 'Tipe prospek harus diisi.',
            'tipe_prospek.in' => 'Tipe prospek tidak valid. Harus salah satu dari: perorangan, bisnis, rekomendasi.',
        ];
    }
}
