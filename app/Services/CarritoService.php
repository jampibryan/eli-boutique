<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use Illuminate\Validation\ValidationException;

class CarritoService
{
    /**
     * Obtiene el carrito actual almacenado en sesión.
     */
    public function getCart(): array
    {
        return session()->get('carrito', []);
    }

    /**
     * Guarda el carrito en sesión.
     */
    public function saveCart(array $carrito): void
    {
        session()->put('carrito', array_values($carrito));
    }

    /**
     * Limpia completamente el carrito de sesión.
     */
    public function clear(): void
    {
        session()->forget('carrito');
    }

    /**
     * Prepara todos los datos requeridos para renderizar la vista del carrito.
     */
    public function getCartViewData(): array
    {
        $carrito = $this->getCart();

        $productos = Producto::with('tallaStocks.talla')
            ->whereIn('id', collect($carrito)->pluck('producto_id'))
            ->get()
            ->keyBy('id');

        $tallas = ProductoTalla::whereIn('id', collect($carrito)->pluck('talla_id'))
            ->get()
            ->keyBy('id');

        $stocksPorTalla = [];
        foreach ($productos as $producto) {
            foreach ($producto->tallaStocks as $tallaStock) {
                $key = $producto->id . '_' . $tallaStock->producto_talla_id;
                $stocksPorTalla[$key] = $tallaStock->stock;
            }
        }

        $puedenDuplicarse = [];
        foreach ($carrito as $item) {
            $productoId = $item['producto_id'];

            if (!isset($puedenDuplicarse[$productoId])) {
                $producto = $productos[$productoId] ?? null;

                if (!$producto) {
                    $puedenDuplicarse[$productoId] = false;
                    continue;
                }

                $tallasConStock = $producto->tallaStocks
                    ->where('stock', '>', 0)
                    ->pluck('producto_talla_id')
                    ->toArray();

                $tallasEnCarrito = collect($carrito)
                    ->where('producto_id', $productoId)
                    ->pluck('talla_id')
                    ->toArray();

                $tallasDisponibles = array_diff($tallasConStock, $tallasEnCarrito);
                $puedenDuplicarse[$productoId] = !empty($tallasDisponibles);
            }
        }

        return compact('carrito', 'productos', 'tallas', 'stocksPorTalla', 'puedenDuplicarse');
    }

    /**
     * Agrega un producto al carrito seleccionando la primera talla con stock disponible.
     */
    public function addItem(int $productoId, int $cantidad = 1): array
    {
        $producto = Producto::with('tallaStocks.talla')->find($productoId);

        if (!$producto) {
            return [
                'success' => false,
                'error' => 'Producto no encontrado.',
            ];
        }

        $primeraTalla = $producto->tallaStocks->where('stock', '>', 0)->first();

        if (!$primeraTalla) {
            return [
                'success' => false,
                'error' => 'Este producto no tiene stock disponible en ninguna talla.',
            ];
        }

        $tallaId = $primeraTalla->producto_talla_id;
        $carrito = $this->getCart();

        // Calcular cantidad existente en el carrito para este producto y talla
        $cantidadEnCarrito = 0;
        foreach ($carrito as $item) {
            if ($item['producto_id'] == $productoId && $item['talla_id'] == $tallaId) {
                $cantidadEnCarrito += $item['cantidad'];
            }
        }

        $stockDisponible = $primeraTalla->stock;
        $cantidadTotalRequerida = $cantidadEnCarrito + $cantidad;

        if ($cantidadTotalRequerida > $stockDisponible) {
            $tallaNombre = $primeraTalla->talla->descripcion ?? '';
            return [
                'success' => false,
                'error' => "Stock insuficiente para {$producto->descripcionP} talla {$tallaNombre}. Stock disponible: {$stockDisponible}, ya en carrito: {$cantidadEnCarrito}",
            ];
        }

        $encontrado = false;
        $indiceEncontrado = -1;

        foreach ($carrito as $index => $item) {
            if ($item['producto_id'] == $productoId && $item['talla_id'] == $tallaId) {
                $encontrado = true;
                $indiceEncontrado = $index;
                break;
            }
        }

        if ($encontrado) {
            $carrito[$indiceEncontrado]['cantidad'] += $cantidad;
        } else {
            $carrito[] = [
                'producto_id' => $productoId,
                'talla_id' => $tallaId,
                'cantidad' => $cantidad,
            ];
            $indiceEncontrado = count($carrito) - 1;
        }

        $this->saveCart($carrito);

        $cantidadTotal = $carrito[$indiceEncontrado]['cantidad'];

        return [
            'success' => true,
            'message' => "Producto agregado al carrito. Total: {$cantidadTotal} unidades.",
            'cantidad_items' => count($carrito),
        ];
    }

    /**
     * Remueve un ítem del carrito según su índice.
     */
    public function removeItem(int $index): void
    {
        $carrito = $this->getCart();
        if (isset($carrito[$index])) {
            unset($carrito[$index]);
            $this->saveCart($carrito);
        }
    }

    /**
     * Incrementa o decrementa la cantidad de un ítem en el carrito validando stock.
     */
    public function updateQuantity(int $index, string $accion): array
    {
        $carrito = $this->getCart();

        if (!isset($carrito[$index])) {
            return ['success' => false, 'message' => 'Producto no encontrado en el carrito.'];
        }

        $item = $carrito[$index];

        $tallaStock = ProductoTallaStock::where('producto_id', $item['producto_id'])
            ->where('producto_talla_id', $item['talla_id'])
            ->first();

        if (!$tallaStock) {
            return ['success' => false, 'message' => 'Talla no encontrada.'];
        }

        $cantidadEnCarritoMismaTalla = 0;
        foreach ($carrito as $carritoItem) {
            if ($carritoItem['producto_id'] == $item['producto_id'] && $carritoItem['talla_id'] == $item['talla_id']) {
                $cantidadEnCarritoMismaTalla += $carritoItem['cantidad'];
            }
        }

        if ($accion === 'aumentar') {
            if ($carrito[$index]['cantidad'] >= $tallaStock->stock) {
                return ['success' => false, 'message' => 'Stock máximo alcanzado.'];
            }

            $nuevaCantidadTotal = $cantidadEnCarritoMismaTalla + 1;
            if ($nuevaCantidadTotal > $tallaStock->stock) {
                return ['success' => false, 'message' => 'Stock insuficiente.'];
            }

            $carrito[$index]['cantidad'] += 1;
            $this->saveCart($carrito);

            return [
                'success' => true,
                'cantidad' => $carrito[$index]['cantidad'],
                'message' => "Cantidad aumentada a {$carrito[$index]['cantidad']} unidades.",
            ];
        }

        if ($accion === 'disminuir') {
            if ($carrito[$index]['cantidad'] > 1) {
                $carrito[$index]['cantidad'] -= 1;
                $this->saveCart($carrito);

                return [
                    'success' => true,
                    'cantidad' => $carrito[$index]['cantidad'],
                    'message' => "Cantidad actualizada a {$carrito[$index]['cantidad']} unidades.",
                ];
            }
        }

        return ['success' => false, 'message' => 'Acción no permitida.'];
    }

    /**
     * Cambia la talla de un ítem en el carrito ciclando entre las tallas disponibles libres.
     */
    public function changeTalla(int $index, string $accion): array
    {
        $carrito = $this->getCart();

        if (!isset($carrito[$index])) {
            return ['success' => false, 'message' => 'Producto no encontrado en el carrito.'];
        }

        $item = $carrito[$index];
        $producto = Producto::with('tallaStocks.talla')->find($item['producto_id']);

        if (!$producto || $producto->tallaStocks->isEmpty()) {
            return ['success' => false, 'message' => 'Producto sin tallas disponibles.'];
        }

        $tallasConStock = $producto->tallaStocks
            ->where('stock', '>', 0)
            ->sortBy('producto_talla_id')
            ->pluck('producto_talla_id')
            ->toArray();

        $tallasOcupadas = [];
        foreach ($carrito as $idx => $carritoItem) {
            if ($idx != $index && $carritoItem['producto_id'] == $item['producto_id']) {
                $tallasOcupadas[] = $carritoItem['talla_id'];
            }
        }

        $tallasLibres = array_diff($tallasConStock, $tallasOcupadas);

        if (!in_array($item['talla_id'], $tallasLibres)) {
            $tallasLibres[] = $item['talla_id'];
        }

        $tallasDisponibles = array_values(array_unique($tallasLibres));
        sort($tallasDisponibles);

        if (count($tallasDisponibles) <= 1) {
            return [
                'success' => false,
                'message' => 'No hay otras tallas disponibles. Todas están ocupadas en el carrito.',
            ];
        }

        $indiceActual = array_search($item['talla_id'], $tallasDisponibles);

        if ($indiceActual === false) {
            return ['success' => false, 'message' => 'Talla actual no válida.'];
        }

        $nuevoIndice = $indiceActual;
        if ($accion === 'siguiente') {
            $nuevoIndice = ($indiceActual + 1) % count($tallasDisponibles);
        } elseif ($accion === 'anterior') {
            $nuevoIndice = ($indiceActual - 1 + count($tallasDisponibles)) % count($tallasDisponibles);
        }

        $nuevaTallaId = $tallasDisponibles[$nuevoIndice];
        $tallaStock = $producto->tallaStocks->firstWhere('producto_talla_id', $nuevaTallaId);

        if (!$tallaStock) {
            return ['success' => false, 'message' => 'Stock de talla no encontrado.'];
        }

        if ($item['cantidad'] > $tallaStock->stock) {
            $carrito[$index]['cantidad'] = $tallaStock->stock;
        }

        $carrito[$index]['talla_id'] = $nuevaTallaId;
        $this->saveCart($carrito);

        return [
            'success' => true,
            'talla_nombre' => $tallaStock->talla->descripcion,
            'talla_id' => $nuevaTallaId,
            'stock' => $tallaStock->stock,
            'cantidad' => $carrito[$index]['cantidad'],
            'message' => "Talla cambiada a {$tallaStock->talla->descripcion}",
        ];
    }

    /**
     * Duplica un ítem del carrito asignándole la siguiente talla libre con stock.
     */
    public function duplicateItem(int $index): array
    {
        $carrito = $this->getCart();

        if (!isset($carrito[$index])) {
            return ['success' => false, 'message' => 'Producto no encontrado en el carrito.'];
        }

        $itemOriginal = $carrito[$index];
        $productoId = $itemOriginal['producto_id'];

        $producto = Producto::with('tallaStocks.talla')->find($productoId);
        if (!$producto) {
            return ['success' => false, 'message' => 'Producto no encontrado.'];
        }

        $tallasConStock = $producto->tallaStocks
            ->where('stock', '>', 0)
            ->sortBy('producto_talla_id')
            ->pluck('producto_talla_id')
            ->toArray();

        $tallasEnCarrito = [];
        foreach ($carrito as $item) {
            if ($item['producto_id'] == $productoId) {
                $tallasEnCarrito[] = $item['talla_id'];
            }
        }

        $tallasDisponibles = array_values(array_diff($tallasConStock, $tallasEnCarrito));

        if (empty($tallasDisponibles)) {
            return [
                'success' => false,
                'message' => 'Ya tienes todas las tallas disponibles de este producto en el carrito.',
            ];
        }

        $nuevaTallaId = $tallasDisponibles[0];

        $carrito[] = [
            'producto_id' => $productoId,
            'talla_id' => $nuevaTallaId,
            'cantidad' => 1,
        ];

        $this->saveCart($carrito);

        return [
            'success' => true,
            'message' => 'Producto duplicado con siguiente talla disponible.',
            'nuevo_index' => count($carrito) - 1,
        ];
    }
}
