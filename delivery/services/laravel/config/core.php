<?php

return [

    // Restricción de lanzamiento. La relación Company -> Branch sigue siendo 1:N.
    'branches_per_company' => 1,

    /*
    |--------------------------------------------------------------------------
    | Media Engine
    |--------------------------------------------------------------------------
    |
    | Driver activo para el almacenamiento de archivos gestionados por el
    | MediaManager. "local" usa el disco de Laravel indicado en
    | MEDIA_DISK. Para S3/Minio/Azure: implementa MediaDriverInterface y
    | añade la clave aquí — MediaManager la resuelve sin más cambios.
    |
    */

    'media' => [
        'driver' => env('MEDIA_DRIVER', 'local'),
        'disk' => env('MEDIA_DISK', 'public'),
    ],

];
