<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'property';
    
    public $timestamps = false;
    
    protected $fillable = array(
        'price',
        'listingID',
        'address',
        'description',
        'features',
        'buildingDetails',
        'pictures',
        'realtors',
        'landDetails',

        'processed',
    );
    
    /*
    public function category(){
        return $this->belongsTo('App\ModelsProductCategory');
    }
    */
    
    public function images(){
        return $this->hasMany('App\Models\PropertyImage', 'property_id');
    }
    
    public function realtors(){
        return $this->hasMany('App\Models\PropertyRealtor', 'property_id');
    }

    public function buildingDetails(){
        return $this->hasMany('App\Models\PropertyBuildingDetails', 'property_id');
    }
    public function landDetails(){
        return $this->hasMany('App\Models\PropertyLandDetails', 'property_id');
    }

}
