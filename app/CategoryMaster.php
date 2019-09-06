<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMaster extends Model
{
    const MASTER_COUNT = 43;
    protected $perPage = 15; //ページングのページ数

    /** JSONに含める属性の指定 */
    protected $visible = [
        'id',
        'name',
    ];

    public function userCategories()
    {
        return $this->hasMany('App\UserCategory');
    }

    public function parent()
    {
        return $this->belongsTo('App\CategoryMaster');
    }

    public function children()
    {
        return $this->hasMany('App\CategoryMaster', 'parent_id');
    }

    public function section()
    {
        return $this->belongsTo('App\CategoryMaster');
    }

    static public function getSectionList()
    {
        return self::whereRaw('section_id = id')->get()->keyBy('id');
    }
}
