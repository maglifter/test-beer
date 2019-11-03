<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    public function type()
    {
        return $this->belongsTo('App\BeerType', 'type_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo('App\Manufacturer', 'manufacturer_id');
    }
}
