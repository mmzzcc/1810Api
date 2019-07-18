<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CateModel extends Model
{
    //
    public $table='shop_category';
    public $primarykeys='cate_id';
    public $timestamps= false;
}
