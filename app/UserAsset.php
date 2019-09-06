<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Exception;

class UserAsset extends Model
{
    /** JSONに含める属性の指定 */
    protected $visible = [
        'assetCategoryIds',
        'name',
        'value',
    ];

    protected $appends = [
        'assetCategoryIds',
        'assetCategoryMasters'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userAssetCategories()
    {
        return $this->belongsToMany('App\UserAssetCategory');
    }

    public function getAssetCategoryMastersAttribute()
    {
        return $this->userAssetCategories->map(function ($uAssetCategory) {
            return $uAssetCategory->assetCategoryMaster;
        });
    }


    public function getAssetCategoryIdsAttribute()
    {
        $returnList = [];
        foreach ($this->assetCategoryMasters as $assetCategory){
            if(isset($returnList[$assetCategory->section_id])){
                throw new Exception('invalid data');
            }
            $returnList[$assetCategory->section_id] = $assetCategory->id;
        }

        return $returnList;
    }
}
