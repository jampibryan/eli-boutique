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
        $clienteId = $this->route('cliente')?->id ?? $this->cliente?->id;

        return [
            'nombreCliente' => 'required|string|max:255',
            'apellidoCliente' => 'required|string|max:255',
            'tipo_genero_id' => 'required|exists:tipo_generos,id',
            'dniCliente' => [
                'required',
                'regex:/^\d{8}$/',
                \Illuminate\Validation\Rule::unique('clientes', 'dniCliente')
                    ->ignore($clienteId)
                    ->whereNull('deleted_at'),
            ],
            'correoCliente' => [
                'nullable',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('clientes', 'correoCliente')
                    ->ignore($clienteId)
                    ->whereNull('deleted_at'),
            ],
            'telefonoCliente' => [
                'nullable',
                'numeric',
                'digits:9',
                \Illuminate\Validation\Rule::unique('clientes', 'telefonoCliente')
                    ->ignore($clienteId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'dniCliente.required' => 'El número de documento es obligatorio.',
            'dniCliente.regex' => 'El documento debe ser un DNI de 8 dígitos numéricos.',
            'dniCliente.unique' => 'El DNI ingresado ya pertenece a otro cliente registrado.',
            'correoCliente.unique' => 'El correo electrónico ya se encuentra registrado.',
            'telefonoCliente.unique' => 'El teléfono ya se encuentra registrado.',
        ];
    }
}