<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyToPropertyFeature extends Model{
    protected $table = 'property_to_property_feature';
    public $timestamps = false;        
    protected $fillable = array(
        'property_id',
        'property_feature_id',
        'value',
        'type'
    );
    
    public function property(){
        return $this->belongsTo('App\Models\PropertyFeature', 'property_feature_id');
    }
    
}
