<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class UserCategory extends Model
{
    use MethodCache;

    const MAX_GOAL_RATIO = 10000;

    protected $cache = [];

    protected $guarded = [
        'id',
    ];

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

    public function categoryMaster()
    {
        return $this->belongsTo('App\CategoryMaster');
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
        return $this->categoryMaster->name;
    }

    public function getCurrentInfoAttribute()
    {
        return [
            'value' => $this->current_value,
            'rate' => $this->current_rate,
        ];
    }

    public function getGoalInfoAttribute()
    {
        return [
            'value' => $this->goal_value,
            'rate' => $this->goal_rate,
        ];
    }

    public function getChildrenAttribute()
    {
        $children = self::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
            ->where([
                ['user_categories.user_id',$this->user_id],
                ['category_masters.parent_id', $this->category_master_id],
                ['user_categories.id','<>', $this->id],
            ])->get();

        $children->each(function ($child) {
           $child->parent = $this;
        });

        return $children;
    }

    public function setParentAttribute(UserCategory $parent)
    {
        $this->cache["parent"] = $parent;
    }

    /**
     * sectionであり親がいない場合はnullを返す
     * @return mixed
     */
    public function getParentAttribute()
    {
        if (isset($this->cache["parent"]) && $this->cache["parent"] instanceof UserCategory){
            return $this->cache["parent"];
        }
        else if($this->hasParent()) {
            return self::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
                ->where([
                    ['user_categories.user_id',$this->user_id],
                    ['category_masters.id', $this->categoryMaster->parent_id],
                ])->first();
        }
        else {
            return null;
        }
    }

    public function getCurrentRateAttribute()
    {
        if(!$this->parent){
            return null;
        }
        else {
            return $this->parent->current_value * $this->goal_rate;
        }
    }

    public function getGoalValueAttribute()
    {
        if(!$this->parent){
            return null;
        }
        else {
            if(!$this->parent->current_value){
                return 0;
            }
            else {
                return $this->current_value / $this->parent->current_value;
            }
        }
    }

    public function hasChild()
    {
        return $this->categoryMaster->has_child;
    }

    public function hasParent()
    {
        return $this->categoryMaster->hasParent();
    }

    /**
     * 自分の値が変わったことを親に伝搬しないので、
     * ルートノードで実行すること
     */
    public function setCurrentValue($isRecursion = false)
    {
        if(!$isRecursion && !$this->isSection()){
            throw new Exception('invalid data. setCurrentValue have to be called as root node(section node)');
        }
        if($this->hasChild()){
            foreach($this->children as $child){
                $child->setCurrentValue(true);
            }
            $this->current_value = $this->children->sum('current_value');
        }
        else {
            $this->current_value = $this->userAssets->sum('value');
        }
    }

    public function getSectionUCategoryId()
    {
        return UserCategory::join('category_masters', 'user_categories.category_master_id', 'category_masters.id')
                ->where('category_masters.id',$this->categoryMaster->section_id)
                ->first();
    }

    public function getName()
    {
        return $this->categoryMaster->name;
    }

    //追加される
    public function addAsset(UserAsset $uAsset)
    {
        $this->userAssets()->save($uAsset);
        $this->setCurrentValue();
    }

}
