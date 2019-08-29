<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userAssetCategories()
    {
        return $this->belongsToMany('App\UserAssetCategory');
    }
}
