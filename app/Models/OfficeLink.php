<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLink extends Model {
    public $timestamps = false;
    protected $table = 'office_link';
    protected $fillable = array(
        'link',
    );
}
