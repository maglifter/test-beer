<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $fillable = [
        'name',
    ];

    public function beers()
    {
        return $this->hasMany('App\Beer','manufacturer_id');
    }
}
