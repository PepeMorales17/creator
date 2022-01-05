<?php

namespace Pp\Creator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "icon",
        "description",
        //"route",
        "namespace",
        "parent_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    const SELECTS = [
        "menus.id as id",
        "menus.name as Nombre",
        "menus.icon as Icono",
        "menus.description as Descripcion",
        "menus.namespace as Nombre ruta",
        "menus.parent_id as Relacion"
    ];

    protected static function booted()
    {
        static::created(function () {
            static::setCache();
        });

        static::updated(function () {
            static::setCache();
        });

        static::deleted(function () {
            static::setCache();
        });
    }

    public static function setCache()
    {
        Cache::forget('menus');
        static::tree();
    }

    // Scopes

    public function scopeView($query)
    {
        $query->select(...self::SELECTS);
    }

    public static function tree()
    {
        return Cache::rememberForever('menus', function () {
            return static::with(implode('.', array_fill(0, 4, 'children')))->where('parent_id', '=', NULL)->get();
        });
    }

    // Relaciones

    public function parent()
    {

        return $this->hasOne(Menu::class, 'id', 'parent_id');
    }

    public function children()
    {

        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }
}
