<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyGroup extends Model
{
    
    public $timestamps = false;
    
    protected $table = 'property_group';
    
    protected $fillable = array(
        //'parent_id', 
        'title', 
        'url',
        'processed'
    );
    
    public function property(){
        return $this->hasMany('App\Models\Property', 'group_id', 'id');
    }
}
