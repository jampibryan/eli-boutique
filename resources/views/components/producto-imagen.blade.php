@props(['src' => null, 'alt' => 'Producto'])

@php
    // Inline fallback: it cannot trigger another request if a photo is missing.
    $fallback = 'data:image/svg+xml;base64,' . base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">'
        . '<rect width="600" height="600" fill="#f5f5f5"/>'
        . '<path d="M240 170l-85 55 45 75 45-25v155h110V275l45 25 45-75-85-55c-10 35-110 35-120 0z" fill="#e5e1d5" stroke="#b49639" stroke-width="6" stroke-linejoin="round"/>'
        . '<text x="300" y="490" text-anchor="middle" font-family="Arial,sans-serif" font-size="24" fill="#666">Sin foto disponible</text></svg>'
    );
    $path = trim((string) $src);
    $url = $path !== '' ? asset(ltrim($path, '/')) : $fallback;
@endphp

<img src="{{ $url }}" alt="{{ $alt }}"
    onerror="this.onerror=null;this.src='{{ $fallback }}'" {{ $attributes }}>
