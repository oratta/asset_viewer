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
        'parent',
        'children',
        'current_rate',
        'goal_value',
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
        return [
            'value' => $this->current_val,
            'rate' => $this->current_rate,
        ];
    }

    public function getGoalInfoAttribute()
    {
        return [
            'value' => $this->goal_val,
            'rate' => $this->goal_rate,
        ];
    }

    public function getChildrenAttribute()
    {
        $children = self::join('asset_category_masters', 'user_asset_categories.asset_category_master_id', '=', 'asset_category_masters.id')
            ->where([
                ['user_asset_categories.user_id',$this->user_id],
                ['asset_category_master.parent_id', $this->asset_category_master_id],
            ])->get();

        return $children;
    }

    public function getParentAttribute()
    {
        return self::join('asset_category_masters', 'user_asset_categories.asset_category_master_id', '=', 'asset_category_masters.id')
            ->where([
                ['user_asset_categories.user_id',$this->user_id],
                ['asset_category_master.id', $this->asset_category_master->parent_id],
            ])->get();
    }

    public function getCurrentRateAttribute()
    {
        return $this->parent->current_val * $this->goal_rate;
    }

    public function getGoalValueAttribute()
    {
        return $this->current_val / $this->parent->current_val;
    }
}
