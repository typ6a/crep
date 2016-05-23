<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'property';
    
    public $timestamps = false;
    
    protected $fillable = array(
        'category_id',
        'url',

        'address',
        'description',
        'price',
        'listingID',
        'features', //noname table at realtor, Property Overview table + Special Features table at rew
        'buildingDetails',//building table at realtor
        //'images',
        //'realtors',
        'landDetails',//land table at realtor
        //'features'
        'processed',
    );




    /*
    public function category(){
        return $this->belongsTo('App\ModelsProductCategory');
    }
    */
    public function group(){
        return $this->belongsTo('App\Models\PropertyGroup');
    }

    public function category(){
        return $this->belongsTo('App\Models\PropertyCategory');
    }

    public function images(){
        return $this->hasMany('App\Models\PropertyImage', 'property_id');
    }

    public function features(){
        return $this->hasMany('App\Models\PropertyToPropertyFeature', 'property_id');
    }

    public function realtors(){
        return $this->hasMany('App\Models\PropertyToPropertyRealtor', 'property_id');
    }

    public function buildingDetails(){
        return $this->hasMany('App\Models\PropertyBuildingDetails', 'property_id');
    }
    public function landDetails(){
        return $this->hasMany('App\Models\PropertyLandDetails', 'property_id');
    }
}
