<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFeature extends Model
{
    
    protected $table = 'property_feature';
    public $timestamps = false;
        
    protected $fillable = array(
        'name'
    );

    public function properties(){
        return $this->belongsToMany('App\Models\Property', 'property_to_property_feature', 'feature_id', 'property_id');
    }
}
