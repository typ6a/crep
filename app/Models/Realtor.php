<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realtor extends Model
{
    protected $table = 'realtor';
    
    public $timestamps = false;
    
    protected $fillable = array(
        'category_id',
        'url',

        'address',
        'description',
        'price',
        'listingID',
        'features', //noname table at realtor, Property Overview table + Special Features table at rew 'summary' at realtor
        'propertyDetails',
        'buildingDetails',//building table at realtor
        //'images',
        //'realtors',
        'landDetails',//land table at realtor
        //'features'
        'saleType', //sale rent or lease
        'processed',
    );



images' => $images,
                    'realtorOfficeTitle' => $realtorOfficeTitle,
                    'realtorPhones' => $realtorPhones,
                    'realtorName' => $realtorName,
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

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
        return $this->hasMany('App\Models\RealtorToProperty', 'property_id');
    }

    public function buildingDetails(){
        return $this->hasMany('App\Models\PropertyBuildingDetails', 'property_id');
    }
    public function landDetails(){
        return $this->hasMany('App\Models\PropertyLandDetails', 'property_id');
    }
}
