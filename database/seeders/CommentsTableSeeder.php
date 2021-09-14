<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use App\Models\Comment;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $count = max((int)$this->command->ask('How many comments would you like?', 150), 1);
        Comment::factory($count)
            ->state(new Sequence(
                fn ($sequence) => [
                    'commentable_id' => BlogPost::all()->random()->id,
                    'commentable_type' => 'App\Models\BlogPost',
                    'user_id' => User::all()->random()->id,
                    'created_at' => $faker->dateTimeBetween('-3 months'),
                ]
            ))
            ->create();
    }
}
