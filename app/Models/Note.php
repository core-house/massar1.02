<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Note extends Model
{
    use HasFactory;
    protected $table = 'notes';
    protected $guarded = ['id'];

    public function noteDetails(): HasMany
    {
        return $this->hasMany(NoteDetails::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_notes', 'note_id', 'item_id')
            ->withPivot('note_detail_name')
            ->withTimestamps();
    }
}
