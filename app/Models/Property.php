<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'property';
    
    public $timestamps = false;

    protected $fillable = array(
        //'category_id',
        'listing_id',
        'price',
        'url',
        'address',
        'sale_type', //sale rent or lease
        'open_house',
        'processed',
    );

    public function group(){
        return $this->belongsTo('App\Models\PropertyGroup');
    }

    public function category(){
        return $this->belongsTo('App\Models\PropertyCategory');
    }
    public function features(){
        return $this->hasMany('App\Models\PropertyToPropertyFeature', 'property_id');
    }

    
    public function images(){
        return $this->hasMany('App\Models\PropertyImage', 'property_id');
    }

    public function realtors(){
        return $this->belongsTo('App\Models\RealtorToProperty');
    }

}
