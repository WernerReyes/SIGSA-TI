<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    //
    protected $table = 'cargo';

    protected $primaryKey = 'id_cargo';

    protected $fillable = [
        'descripcion_cargo',
    ];
}
