<?php

namespace Modules\CRM\Models;

use App\Traits\WithSorting;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use WithSorting;
    protected $fillable = ['title'];
}
