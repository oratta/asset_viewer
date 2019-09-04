<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userAssets()
    {
        return $this->hasMany('App\UserAsset');
    }

    public function userAssetCategories()
    {
        return $this->hasMany('App\UserAssetCategory');
    }

    /**
     * @return array
     */
    public function getNestedUserAssetCategoryList() : Collection
    {
        $query = UserAssetCategory::join('asset_category_masters', 'user_asset_categories.asset_category_master_id', '=', 'asset_category_masters.id')
            ->whereRaw(
                "asset_category_masters.section_id = asset_category_masters.id and 
                        user_asset_categories.user_id=$this->id"
            );
        $uSectionList = $query->get();

        return $uSectionList;
    }
}
