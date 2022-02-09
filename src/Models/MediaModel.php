<?php

namespace Pp\Creator\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaModel extends Model implements HasMedia
{
    use InteractsWithMedia;

}
