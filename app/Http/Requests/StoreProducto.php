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
            'codigoP' => 'required|string|max:50',
            'categoria_producto_id' => 'required|exists:categoria_productos,id',
            'producto_genero_id' => 'required|exists:producto_generos,id',
            'imagenP' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'descripcionP' => 'required|string',
            'precioP' => 'required|numeric|min:0',
        ];
    }
}
