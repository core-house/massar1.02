<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTybe extends Model
{
    use HasFactory;

    // تحديد اسم الجدول (اختياري إذا كان اسم الجدول مطابقاً للاسم الافتراضي)
    protected $table = 'journal_tybes';

    // تحديد الأعمدة القابلة للتحديث
    protected $fillable = [
        'journal_id', 'jname', 'jtext', 'info', 'isdeleted', 'tenant', 'branch'
    ];

    // تحديد الأعمدة التي لا يمكن تحديثها (إذا كانت موجودة)
    protected $guarded = ['id', 'crtime', 'mdtime'];
}
