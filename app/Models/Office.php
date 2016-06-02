<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'office';
    
    public $timestamps = false;

    protected $fillable = array(
        'image_name',
        'image_url',
        'name',
        'destination',
        'address',
    );

    public function officePhones()
    {
        return $this->hasMany('App\Models\OfficePhone', 'office_id');
    }

    public function officeLinks()
    {
        return $this->hasMany('App\Models\OfficeLink', 'office_id');
    }

    public function realtors()
    {
        return $this->hasMany('App\Models\Realtor', 'office_id');
    }
}
