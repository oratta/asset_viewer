<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssetCategory extends Model
{
    const MAX_GOAL_RATIO = 10000;
    public function assetCategory()
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
}
