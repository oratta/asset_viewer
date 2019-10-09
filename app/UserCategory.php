<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Exception;

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
            'value' => number_format($this->current_value),
            'rate' => round($this->current_rate,2),
        ];
    }

    public function getGoalInfoAttribute()
    {
        return [
            'value' => number_format($this->goal_value),
            'rate' => round($this->goal_rate,2),
        ];
    }

    public function getChildrenAttribute()
    {
        if($this->isCached())return $this->getCache();
        $children = self::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
            ->where([
                ['user_categories.user_id',$this->user_id],
                ['category_masters.parent_id', $this->category_master_id],
                ['user_categories.id','<>', $this->id],
            ])->get();

        $children->each(function ($child) {
           $child->parent = $this;
        });

        $this->setCache($children);
        return $this->getCache();
    }

    public function setChildrenAttribute(Collection $children)
    {
        $this->setCache($children, 'children');
    }

    public function getChildrenIds()
    {
        return $this->children->map(function($child){
            return $child->id;
        })->toArray();
    }

    public function setParentAttribute(UserCategory $parent)
    {
        $this->setCache($parent,'parent');
    }

    /**
     * sectionであり親がいない場合はnullを返す
     * @return mixed
     */
    public function getParentAttribute()
    {
        if($this->isCached()) return $this->getCache();

        if($this->hasParent()) {
            $parent = self::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
                ->where([
                    ['user_categories.user_id',$this->user_id],
                    ['category_masters.id', $this->categoryMaster->parent_id],
                ])->first();
        }
        else {
            $parent = null;
        }

        $this->setCache($parent);
        return $parent;
    }

    public function getCurrentRateAttribute()
    {
        if(!$this->parent){
            return null;
        }
        else {
            return $this->parent->current_value ? $this->current_value / $this->parent->current_value * 100 : 0;
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
        if(!$isRecursion && !$this->categoryMaster->isSection()){
            throw new Exception('invalid data. setCurrentValue have to be called as root node(section node)');
        }
        if($this->hasChild()){
            foreach($this->children as $child){
                $child->setCurrentValue(true);
                $child->save();
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
                ->where([
                    ['user_categories.user_id','=',$this->user_id],
                    ['category_masters.id','=',$this->categoryMaster->section_id]
                    ])
                ->first()->id;
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
