<?php

namespace App\Traits;

trait Filterable
{
    /**
     * Scope genérico para aplicar filtros de búsqueda por campos y rangos de fechas.
     */
    public function scopeFilter($query, array $filters = [])
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchableFields = property_exists($this, 'searchableFields') 
                ? $this->searchableFields 
                : ['nombre', 'codigoVenta', 'razon_social'];

            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $index => $field) {
                    if ($index === 0) {
                        $q->where($field, 'LIKE', "%{$search}%");
                    } else {
                        $q->orWhere($field, 'LIKE', "%{$search}%");
                    }
                }
            });
        }

        if (!empty($filters['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['fecha_inicio']);
        }

        if (!empty($filters['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $filters['fecha_fin']);
        }

        return $query;
    }
}
