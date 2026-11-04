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
        $proveedorId = $this->route('proveedore')?->id ?? $this->route('proveedor')?->id ?? $this->proveedore?->id;

        return [
            'nombreEmpresa' => 'required|string|max:255',
            'nombreProveedor' => 'required|string|max:255',
            'apellidoProveedor' => 'required|string|max:255',
            'RUC' => [
                'required',
                'regex:/^(10|15|17|20)\d{9}$/',
                \Illuminate\Validation\Rule::unique('proveedores', 'RUC')
                    ->ignore($proveedorId)
                    ->whereNull('deleted_at'),
            ],
            'direccionProveedor' => 'required|string|max:255',
            'correoProveedor' => [
                'required',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('proveedores', 'correoProveedor')
                    ->ignore($proveedorId)
                    ->whereNull('deleted_at'),
            ],
            'telefonoProveedor' => [
                'required',
                'numeric',
                'digits:9',
                \Illuminate\Validation\Rule::unique('proveedores', 'telefonoProveedor')
                    ->ignore($proveedorId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'RUC.required' => 'El número de RUC es obligatorio.',
            'RUC.regex' => 'El RUC debe tener 11 dígitos y comenzar con 10, 15, 17 o 20.',
            'RUC.unique' => 'El número de RUC ya se encuentra registrado.',
            'correoProveedor.unique' => 'El correo electrónico ya se encuentra registrado.',
            'telefonoProveedor.unique' => 'El teléfono ya se encuentra registrado.',
        ];
    }
}

