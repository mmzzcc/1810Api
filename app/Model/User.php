<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    public $table='user';
    public $primarykeys='u_id';
    public $timestamps= false;
}
