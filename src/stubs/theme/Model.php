<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent {
    protected $guarded = [];
    protected $perPage = 10;
}
