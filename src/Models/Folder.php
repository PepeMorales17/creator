<?php

namespace Pp\Creator\Models;

use Pp\Creator\Models\MediaModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Folder extends MediaModel
{
    use HasFactory;

    protected $table = 'folders';

    protected $fillable = [
        "name",
        "parent_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    const SELECTS = [
        "folders.id as id",
        "folders.name as Nombre",
        "folders.parent_id as Raiz"
    ];

    // // boot

    // protected static function booted()
    // {
    //     static::deleting(function ($Folder) {
    //          return true;
    //     });
    // }

    const STOP_DELETE_MSG = 'No puedes borrar ';

    // Scopes

    public function scopeView($query)
    {
        $query->select(...self::SELECTS);
    }

    public static function tree()
    {
        $nested = static::with(implode('.', array_fill(0, 4, 'children')))->where('parent_id', '=', NULL)->get();
        $setName = function ($folder, $index = null, $parent = null) use (&$setName) {
            $folder->root = (!!$parent ? (!!$parent->root ? $parent->root : '') : '') . '/' . $folder->name;
            $folder->root_index = (!!$parent ? (isset($parent->root_index) ? $parent->root_index. '.children.' : '') : '')  . $index;
            $folder->children->map(fn ($fo, $index) => $setName($fo, $index, $folder));
        };
        $nested->map(fn ($fo, $index) => $setName($fo, $index));
        return $nested;
    }


    // Relaciones

    public function parent()
    {

        return $this->hasOne(Folder::class, 'id', 'parent_id');
    }

    public function children()
    {

        return $this->hasMany(Folder::class, 'parent_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(Media::class, 'collection_name', 'id');
    }
}
