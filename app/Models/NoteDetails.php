<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteDetails extends Model
{
    //
    protected $table = 'note_details';
    protected $guarded = ['id'];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
