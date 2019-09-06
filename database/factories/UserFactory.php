<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\UserCategory;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->afterCreatingState(App\User::class, 'withAsset', function ($user, $faker) {
    $uCategoryList = [];
    for($i=1; $i<=\App\CategoryMaster::MASTER_COUNT; ++$i){
        $uCategoryList[] = factory(App\UserCategory::class)->create(['user_id'=>$user->id, 'category_master_id'=>$i]);
    }
    foreach($uCategoryList as $uCategory){
        $uCategory->userAssets()->saveMany(factory(App\UserAsset::class,5)->make(['user_id' => $user->id]));
    }
});