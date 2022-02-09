<?php

use Illuminate\Support\Facades\Route;
use Pp\Creator\Http\Controllers\FolderController;
use Pp\Creator\Http\Controllers\MenuController;


Route::controller('Pp\Creator\Http\Controllers\FolderController')->prefix('folder')->name('folder.')->group(function () {
    Route::get('main', 'tree')->name('main');
    Route::get('download/{id}', 'download')->name('download');
    Route::get('model/{table}/{id}', 'modelFiles')->name('model');
    Route::get('files/{id}','folderFiles')->name('files');
    Route::post('upload','upload')->name('upload');
    Route::post('deleteFile/{id}','deleteFile')->name('delete_file')->query;
});

Route::resources([
    'menu' => MenuController::class,
    'folder'  => FolderController::class,
]);
