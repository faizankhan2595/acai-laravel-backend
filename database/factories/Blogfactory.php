<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Blog;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

$factory->define(Blog::class, function (Faker $faker) {
    $imgary = ['blog/jJtrJ0JD4TAYNtToDch90MHFaRa6ssFGEyDBqWFD.jpeg','blog/mTFzrab1uu4q0Kd9eruAsoCFzNmCh4RXuphKneNt.png',null];
    $vidary = ['https://www.youtube.com/watch?v=W70wjVFKaDc',null];
    return [
        'category_id' => rand(2,3),
        'title' => $faker->realText($maxNbChars = 20),
        'slug' => Str::slug($faker->realText($maxNbChars = 20)),
        'user_id' => 1,
        'post_body' => $faker->realText,
        'is_featured' => 0,
        'featured_image' => $imgary[array_rand($imgary)],
        'featured_video' => $vidary[array_rand($vidary)],
        'published_on' => Carbon::now(),
        'allow_comments' => rand(0,1),
        'status' => rand(0,1),
    ];
});
