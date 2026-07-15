<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LexiconSeeder extends Seeder
{
    public function run(): void
    {
        $positives = ['growth', 'increase', 'profit', 'stable', 'improve', 'success', 'recovery', 'strong', 'positive', 'gain'];
        foreach ($positives as $word) {
            DB::table('positive_words')->updateOrInsert(['word' => $word], ['created_at' => now(), 'updated_at' => now()]);
        }

        $negatives = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'disruption', 'decrease', 'loss', 'shortage', 'risk', 'fail', 'bad', 'drop', 'block'];
        foreach ($negatives as $word) {
            DB::table('negative_words')->updateOrInsert(['word' => $word], ['created_at' => now(), 'updated_at' => now()]);
        }
    }
}
