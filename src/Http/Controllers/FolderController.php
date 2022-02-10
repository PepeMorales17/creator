<?php

namespace Pp\Creator\Http\Controllers;

use Pp\Creator\Forms\CreateFolderForm;
use App\Http\Controllers\Controller;
use Pp\Creator\Models\Folder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Pp\Creator\Traits\Controllers\CrudTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FolderController extends Controller
{
    use CrudTrait;

    protected $view = 'File/CrudFolder';
    protected $redirect_route = 'folder.index';
    protected $type = 'Folders';
    protected $folder = 'File';

    public function form()
    {
        return new CreateFolderForm();
    }

    public function index()
    {
        return $this->justRenderIndex(Folder::view()->get());
    }

    public function store(Request $request)
    {
        $data = $this->form()->validate($request->all());
        //dd($data, $request->all());
        DB::transaction(function () use ($data) {
            Folder::create($data);
        });
        if ($request->wantsJson()) return Folder::tree();
        return $this->mainRedirect('Folder guardado');
    }

    public function show($id)
    {
        $model = Folder::view()->findOrFail($id);
        return $this->justShow($model);
    }

    public function edit(Folder $folder)
    {
        return $this->justEdit($this->form()->getFormToEditWithEmptyValue(), $folder);
    }

    public function update(Request $request, Folder $folder)
    {
        $data = $this->form()->validate($request->all());
        DB::transaction(function () use ($data, $folder) {
            $folder->update($data);
        });

        return $this->mainRedirect('Folder editado');
    }

    public function destroy(Folder $folder)
    {
        $deletion = DB::transaction(function () use ($folder) {
            return  $folder->delete();
        });
        if (!$deletion) {
            return $this->mainRedirect(Folder::STOP_DELETE_MSG, false);
        }
        return $this->mainRedirect('Folder borrado');
    }

    // api

    public function tree()
    {
        return Folder::tree();
    }

    public function download($id)
    {
        return Media::findOrFail($id);
    }

    const PICK = ['file_name', 'id', 'model_type', 'model_id', 'url', 'mime_type', 'size'];

    public function modelFiles($table, $id)
    {
        //dd(Media::where(['model_id' => $id, 'model_type' => $table,])->get()->map(fn($f) => $this->withUrl($f))->pick(self::PICK)->toArray());
        return Media::where(['model_id' => $id, 'model_type' => Str::plural($table),])->get()->map(fn($f) => $this->withUrl($f))->pick(self::PICK)->toArray();
    }

    public function withUrl($file)
    {
        $file->url = $file->getUrl();
        //dd($file->toArray());
        return $file;
    }

    public function folderFiles($id)
    {
        return Media::where('collection_name', $id)->get()->map(fn($f) => $this->withUrl($f))->pick(self::PICK)->toArray();
    }

    public function upload(Request $request)
    {
        $folder = $request->folder ? $request->folder : 1;
        $isModel = !!$request->id;
        $model = null;
        if ($isModel) {
            $class = Relation::morphMap()[Str::plural($request->model)];
            $model = $class::findOrFail($request->id);
        } else {
            $model = Folder::findOrFail($folder);
        }
        if (!!$model) collect($request->file('files'))->map(fn ($f) => $model->addMedia($f)->toMediaCollection($folder));
        //dd
        return !$request->inFolder ? $this->modelFiles($request->model, $request->id) : $this->folderFiles($folder);
    }

    public function deleteFile(Request $request)
    {
        Media::findOrFail($request->id);
    }
}