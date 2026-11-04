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
        $clienteId = $this->route('cliente')?->id;
        $empresa = $this->input('tipo_documento', 'DNI') === 'RUC';

        return [
            'nombreCliente' => 'required|string|max:255',
            'apellidoCliente' => ($empresa ? 'nullable' : 'required').'|string|max:255',
            'tipo_documento' => 'required|in:DNI,RUC',
            'direccionCliente' => ($empresa ? 'required' : 'nullable').'|string|max:255',
            'tipo_genero_id' => 'required|exists:tipo_generos,id',
            'dniCliente' => [
                'required',
                $empresa ? 'regex:/^\d{11}$/' : 'regex:/^\d{8}$/',
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

    protected function prepareForValidation(): void
    {
        $type = $this->input('tipo_documento', $this->route('cliente')?->tipo_documento ?? 'DNI');
        $this->merge(['tipo_documento'=>$type]);
        if ($type === 'RUC') {
            $this->merge([
                'apellidoCliente'=>'',
                'tipo_genero_id'=>\App\Models\TipoGenero::where('descripcionTG','No aplica')->value('id'),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'dniCliente.required' => 'El número de documento es obligatorio.',
            'dniCliente.regex' => 'Ingresa un DNI de 8 dígitos o un RUC de 11 dígitos, según el tipo seleccionado.',
            'dniCliente.unique' => 'El documento ingresado ya pertenece a otro cliente registrado.',
            'correoCliente.unique' => 'El correo electrónico ya se encuentra registrado.',
            'telefonoCliente.unique' => 'El teléfono ya se encuentra registrado.',
        ];
    }
}
