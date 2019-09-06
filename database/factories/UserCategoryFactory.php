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
            return factory(App\User::class)->crate()->id;
        },
        'goal_ratio' => function () {
            //goal_ratioのmax値を定数化
            return mt_rand(0, UserCategory::MAX_GOAL_RATIO);
        },
        'current_value' => 0
    ];
});

$factory->afterCreating(App\UserCategory::class, function ($userCategory, $faker) {
        if($userCategory->hasChild()){
            return;
        }
        $userAssetList = factory(App\UserAsset::class,10)->create(["user_id"=>$userCategory->user_id]);
        $userCategory->userAssets()->saveMany($userAssetList);
        $userCategory->current_value = $userAssetList->sum('value');
        $userCategory->save();
});
