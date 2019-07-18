<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    public $table='user_token';
    public $primarykeys='t_id';
    public $timestamps= false;
}
