<?php

namespace Pp\Creator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "icon",
        "description",
        "route",
        "namespace",
        "parent_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    const SELECTS = [
        "menus.id as 'Id'",
        "menus.name as 'Nombre'",
        "menus.icon as 'Icono'",
        "menus.description as 'Descripcion'",
        "menus.route as 'Ruta'",
        "menus.namespace as 'Nombre ruta'",
        "menus.parent_id as 'Relacion'"
    ];

    // Scopes

    public function scopeView($query)
    {
        $query->select(...self::SELECTS);
    }

    // Relaciones



}
