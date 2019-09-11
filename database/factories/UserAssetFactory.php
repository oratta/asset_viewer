<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\UserAsset;
use Faker\Generator as Faker;

$factory->define(UserAsset::class, function (Faker $faker) {
    return [
        'user_id' => function(){
            return factory(App\User::class)->crate()->id;
        },
        'name' => $faker->streetName,
        'account' => $faker->streetName,
        'value' => function(){
            return mt_rand(1, 1000000);
        }
    ];
});
