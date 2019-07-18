<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    //
    public $table='email_list';
    public $primarykeys='id';
    public $timestamps= false;
}
