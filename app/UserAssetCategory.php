<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssetCategory extends Model
{
    const MAX_GOAL_RATIO = 10000;

    protected $visible = [
        'name',
        'current_info',
        'goal_info',
        'children',
    ];

    protected $appends = [
        'name',
        'current_info',
        'goal_info',
        'children',
    ];

    public function assetCategoryMaster()
    {
        return $this->belongsTo('App\AssetCategoryMaster');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userAssets()
    {
        return $this->belongsToMany('App\UserAsset');
    }

    public function getNameAttribute()
    {
        return $this->assetCategoryMaster->name;
    }

    public function getCurrentInfoAttribute()
    {
        //TODO
    }

    public function getGoalInfoAttribute()
    {
        //TODO
    }

    public function getChildrenAttribute()
    {
        //TODO
    }
}
