<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    //
    public $table='user_login';
    public $primarykeys='id';
    public $timestamps= false;
}
