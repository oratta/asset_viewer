<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\UserAssetCategory;
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
    for($i=1;$i<=\App\AssetCategoryMaster::MASTER_COUNT;++$i){
        factory(App\UserAssetCategory::class)->create(['user_id'=>$user->id, 'asset_category_master_id'=>$i]);
    }
});