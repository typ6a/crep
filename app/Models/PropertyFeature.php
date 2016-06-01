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
    
}
