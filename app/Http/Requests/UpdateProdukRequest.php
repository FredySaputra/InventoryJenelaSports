<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'Admin';
    }

    public function rules(): array
    {
        return [
            'nama'       => 'required|string|max:100',
            'warna'      => 'nullable|string|max:50',
            'idKategori' => 'required|exists:kategoris,id',
            'idBahan'    => 'required|exists:bahans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'idKategori.exists' => 'Kategori tidak valid.',
            'idBahan.exists'    => 'Bahan tidak valid.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()
        ], 400));
    }
}
