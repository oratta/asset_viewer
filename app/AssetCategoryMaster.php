<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetCategoryMaster extends Model
{
    protected $perPage = 15; //ページングのページ数

    /** JSONに含める属性の指定 */
    protected $visible = [
        'id',
        'name',
    ];

    public function userAssetCategories()
    {
        return $this->hasMany('App\UserAssetCategory');
    }
}
