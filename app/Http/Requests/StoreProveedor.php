<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedor extends FormRequest
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
            // 'tipo_proveedor_id' => 'required',
            'nombreEmpresa' => 'required',
            'nombreProveedor' => 'required',
            'apellidoProveedor' => 'required',
            'RUC' => 'required|digits:11',
            'direccionProveedor' => 'required',
            'correoProveedor' => 'required|email',
            'telefonoProveedor' => 'required|numeric|digits:9',
        ];
    }
}

