<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMaster extends Model
{
    const MASTER_COUNT = 43;
    const LEAF_NODE_COUNT = 33; //葉ノードの数(子を持たないノードの数)
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

    public function getParentAttribute()
    {
        if($this->hasParent()){
            return null;
        }
        return $this->parent()->get();
    }

    public function hasParent()
    {
        if($this->parent_id && $this->parent_id !== $this->id){
            return true;
        }
        return false;
    }
}
