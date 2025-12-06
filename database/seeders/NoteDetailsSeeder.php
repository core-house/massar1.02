<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteDetails;
use Illuminate\Database\Seeder;

class NoteDetailsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Note::all() as $note) {
            NoteDetails::firstOrCreate(
                [
                    'note_id' => $note->id,
                    'name' => $note->name.' 1',
                ]
            );
        }
    }
}
