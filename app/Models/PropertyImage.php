<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model {
    public $timestamps = false;
    protected $table = 'property_image';
    protected $fillable = array(
        'property_id',
        'url',
        'filename',
    );

}
