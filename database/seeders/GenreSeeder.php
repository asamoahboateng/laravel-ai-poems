<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Nursery Rhyme' => 'Traditional rhymes and songs for young children.',
            'Lullaby' => 'Gentle songs meant to soothe children to sleep.',
            'Counting Rhyme' => 'Rhymes that help children learn to count.',
            'Action Rhyme' => 'Rhymes with physical movements and actions.',
            'Limerick' => 'Humorous five-line poems with an AABBA rhyme scheme.',
            'Haiku' => 'Short three-line poems with a 5-7-5 syllable structure.',
            'Free Verse' => 'Poetry without a fixed rhyme or meter.',
        ];

        foreach ($genres as $name => $description) {
            Genre::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
            ]);
        }
    }
}
