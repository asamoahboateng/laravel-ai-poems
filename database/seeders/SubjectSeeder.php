<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Animals' => 'Poems about creatures great and small.',
            'Nature' => 'Poems about the natural world, weather, and seasons.',
            'Family' => 'Poems about parents, siblings, and family life.',
            'Bedtime' => 'Poems for winding down and going to sleep.',
            'Counting' => 'Poems that involve numbers and counting.',
            'Seasons' => 'Poems about spring, summer, autumn, and winter.',
            'Friendship' => 'Poems about friends and companionship.',
            'Adventure' => 'Poems about journeys, exploration, and discovery.',
        ];

        foreach ($subjects as $name => $description) {
            Subject::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
            ]);
        }
    }
}
