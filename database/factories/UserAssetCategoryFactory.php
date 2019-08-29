<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\UserAssetCategory;
use Faker\Generator as Faker;

$factory->define(UserAssetCategory::class, function (Faker $faker) {
    return [
        'asset_category_id' => function(){
            //TODO csvファイルからファイル行数取得
            return mt_rand(1, 43);
        },
        'user_id' => function(){
            return factory(App\User::class)->crate()->id;
        },
        'goal_ratio' => function(){
            //goal_ratioのmax値を定数化
            return mt_rand(0, UserAssetCategory::MAX_GOAL_RATIO);
        },
        'current_val' => 0
    ];

    $factory->afterCreating(UserAssetCategory::class, function ($userAssetCategory, $faker) {
        $userAssetList = $userAssetCategory->userAssets()->save(factory(App\UserAsset::class,10))->create(["user_id"=>$userAssetCategory->user_id]);
        $userAssetCategory->current_val = $userAssetList->sum('val');
        $userAssetCategory->save();
    });
});
