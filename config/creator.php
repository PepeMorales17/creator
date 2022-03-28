<?php

use Pp\Creator\Forms\CreateFolderForm;
use Pp\Creator\Http\Controllers\FolderController;
use Pp\Creator\Models\Folder;

return [

    /**
     * Para que la app funcione tienes que hacer lo siguiente:

     * Ejecuta los siguientes comandos
     * npm i @ppjmorales/creator_template;composer require spatie/laravel-medialibrary; php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations";php artisan migrate; php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config";
     * Agrega en filesystems.php

     'disks' => [
            ...
            'media' => [
                'driver' => 'local',
                'root'   => storage_path('app/media'),
                'url'    => env('APP_URL').'/media',
                'visibility' => 'public',
            ],
            ...
      ],

     'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('images') => storage_path('app/images'),
        public_path('media') => storage_path('app/media'),
    ],

    * En el file \config\media-library.php

    'disk_name' => 'media',

    * php artisan storage:link
    * No olvides cambiar la ulr de ENV para que funcione el storage
     */

    /** QUIERES GUARDAR ARCHIVOS */
    'app_use_media' => true,
    'table_menu' => 'menus',
    'class_menu' => 'Pp\Creator\Models\Menu',

    'cruds' => [
        /**CrudEnd */
    ],


];
