<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMaster extends Model
{
    const MASTER_COUNT = 43;
    const LEAF_NODE_COUNT = 33; //葉ノードの数(子を持たないノードの数)
    protected $perPage = 15; //ページングのページ数

    protected $casts = [
        'parent_id' => 'int', // added_onはいつでもDATE型で取得する
    ];

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
        if(isset($this->attributes['parent'])){
            return $this->attributes['parent'];
        }
        else {
            if(!$this->hasParent()){
                return null;
            }
            $this->attributes['parent'] = $this->parent()->first();
            $this->relations['parent'] = $this->attributes['parent'];
            return $this->attributes['parent'];
        }
    }

    public function hasParent()
    {
        if(!isset($this->parent_id)) throw new \Exception('fail to refer parent_id');
        if($this->parent_id && $this->parent_id !== $this->id){
            return true;
        }
        return false;
    }

    public function isSection()
    {
        if(!isset($this->section_id)) throw new \Exception('fail to refer section_id');
        if($this->section_id && $this->section_id === $this->id){
            return true;
        }
        return false;
    }

    public function setFormattedName()
    {
        $this->name = $this->getFormattedName();
        return true;
    }

    protected function getFormattedName( $formattedName = '')
    {
        if($this->isFormattedName){
            return $this->name;
        }
        else {
            if($this->hasParent() && !$this->parent->isSection()){
                $formattedName = $this->parent->getFormattedName() . "->";
            }
            $this->isFormattedName = true;
            return $formattedName . $this->name;
        }
    }
}
