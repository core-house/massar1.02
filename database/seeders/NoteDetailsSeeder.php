<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Note;

class NoteDetailsSeeder extends Seeder
{
    public function run()
    {
        foreach (Note::all() as $note) {
            DB::table('note_details')->insert([
                [
                    'note_id' => $note->id,
                    'name' => $note->name . ' 1',
                ],
            ]);
        }
    }
}
