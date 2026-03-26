<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $fillable = ['type'];

    public $timestamps = false;  // إذا كنت لا تحتاج إلى التواريخ "created_at" و "updated_at"
}
