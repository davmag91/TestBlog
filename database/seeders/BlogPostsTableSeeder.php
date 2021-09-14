<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class BlogPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $count = max((int)$this->command->ask('How many posts would you like?', 50),1);
        BlogPost::factory($count)
            ->state(new Sequence(
                fn ($sequence) => [
                    'user_id' => User::all()->random()->id,
                    'created_at' => $faker->dateTimeBetween('-3 months'),
                    ]
            ))
            ->create();
    }
}
