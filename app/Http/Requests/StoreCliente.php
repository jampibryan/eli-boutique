<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCliente extends FormRequest
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
        return [
            'nombreCliente' => 'required',
            'apellidoCliente' => 'required',
            'tipo_genero_id' => 'required',
            'dniCliente' => 'required|regex:/^\d{8}$/',
            'correoCliente' => 'nullable|email',
            'telefonoCliente' => 'nullable|numeric|digits:9',
        ];
    }

    public function messages(): array
    {
        return [
            'dniCliente.required' => 'El número de documento es obligatorio.',
            'dniCliente.regex' => 'El documento debe ser un DNI de 8 dígitos.',
        ];
    }
}