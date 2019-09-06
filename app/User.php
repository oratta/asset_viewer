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

    public function userCategories()
    {
        return $this->hasMany('App\UserCategory');
    }

    /**
     * @return array
     */
    public function getNestedUserCategoryList() : Collection
    {
        $query = UserCategory::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
            ->whereRaw(
                "category_masters.section_id = category_masters.id and 
                        user_categories.user_id=$this->id"
            );
        $uSectionList = $query->get();

        return $uSectionList;
    }
}
