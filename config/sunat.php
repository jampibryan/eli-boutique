<?php

return [
    'mode' => env('SUNAT_MODE', 'beta'), // 'beta' o 'production'

    'beta' => [
        'ruc' => '20000000001',
        'usuario' => 'MODDATOS',
        'clave' => 'moddatos',
        'endpoint' => 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService',
    ],

    'production' => [
        'ruc' => env('SUNAT_RUC'),
        'usuario' => env('SUNAT_USUARIO'),
        'clave' => env('SUNAT_CLAVE'),
        'endpoint' => 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService',
    ],

    'certificate' => [
        'path' => storage_path(env('SUNAT_CERTIFICATE_PATH', 'app/certificates/certificate.crt')),
        'key_path' => storage_path(env('SUNAT_PRIVATE_KEY_PATH', 'app/certificates/private.key')),
    ],
];
