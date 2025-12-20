<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProducto extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            'codigoP' => 'required',
            'categoria_producto_id' => 'required',
            'imagenP' => 'nullable|image', // Hacer que la imagen sea opcional
            'descripcionP' => 'required',
            'precioP' => 'required',
            'stockP' => 'required',
        ];

    }
}
