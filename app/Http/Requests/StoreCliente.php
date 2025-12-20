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
            'dniCliente' => 'required|digits:8',
            'correoCliente' => 'required|email',
            'telefonoCliente' => 'required|numeric|digits:9',
        ];
    }
}