<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $notes = [
            'المجموعات',
            'التصنيفات',
            'الاماكن',
        ];

        foreach ($notes as $noteName) {
            Note::firstOrCreate(
                ['name' => $noteName]
            );
        }
    }
}
