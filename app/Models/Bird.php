<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bird extends Model
{
    protected $table = "bird";
    protected $fillable = ['name', 'count'];
}
