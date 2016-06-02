<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realtor extends Model
{
    protected $table = 'realtor';
    
    public $timestamps = false;
    
    protected $fillable = array(

        'name',
        'title',
        'image_name',
        'image_url',

    );

    public function realtorPhones()
    {
        return $this->hasMany('App\Models\RealtorPhone', 'office_id');
    }

    public function realtorLinks()
    {
        return $this->hasMany('App\Models\RealtorLink', 'office_id');
    }

    public function realtorProperties()
    {
        return $this->hasMany('App\Models\Property', 'realtor_id');
    }

}
