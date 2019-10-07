<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\UserAsset;
use App\UserCategory;
use Faker\Generator as Faker;

$factory->define(UserCategory::class, function (Faker $faker) {
    return [
        'category_master_id' => function () {
            //TODO csvファイルからファイル行数取得
            return mt_rand(1, 43);
        },
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'goal_ratio' => function () {
            //goal_ratioのmax値を定数化
            return mt_rand(0, UserCategory::MAX_GOAL_RATIO);
        },
        'current_value' => 0
    ];
});