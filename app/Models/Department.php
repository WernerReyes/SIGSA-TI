<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $table = 'ost_dept';
    protected $primaryKey = 'dept_id';

    public function users() {
        return $this->hasMany(User::class, 'dept_id', 'dept_id');
    }
}
