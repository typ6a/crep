<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    
    public $timestamps = false;
    
    protected $table = 'property_category';
    
    protected $fillable = array(
        //'parent_id', 
        'title', 
        'url',
        'processed'
    );
    
    public function property(){
        return $this->hasMany('App\Models\Property', 'category_id', 'id');
    }
}
