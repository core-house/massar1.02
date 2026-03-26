<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model 
{

    protected $table = 'jobs';
    public $timestamps = true;
    protected $fillable = array('title', 'description', 'salary');

    public function employees()
    {
        return $this->hasMany('App\Models\Employe');
    }

}