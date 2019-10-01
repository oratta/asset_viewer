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
    public function getSections(Collection $uCategoryAll = null) : Collection
    {
        if($uCategoryAll){
            $uSectionList = $uCategoryAll->filter(function($uCategory){
                return $uCategory->categoryMaster->isSection();
            });
        }
        else {
            $query = UserCategory::join('category_masters', 'user_categories.category_master_id', '=', 'category_masters.id')
                ->whereRaw(
                    "category_masters.section_id = category_masters.id and 
                        user_categories.user_id=$this->id"
                )->with(['categoryMaster']);
            $uSectionList = $query->get();
        }

        return $uSectionList;
    }

    public function getNestedUserCategories()
    {
        $uCategoryAll = $this->getUserCategoriesWithMaster();

        $sections = $this->getSections($uCategoryAll);
        $sections = $this->setUserCategoriesNest($sections, $uCategoryAll);

        return $sections;
    }

    /**
     * 下方向にネストする。
     * @param Collection $baseUCategories
     * @param Collection $uCategoryAll category all with master
     */
    private function setUserCategoriesNest(Collection &$baseUCategories, Collection $uCategoryAll)
    {
        foreach($baseUCategories as &$uCategory){
            $this->setChildrenNest($uCategory, $uCategoryAll);
        }
    }

    /**
     * @param UserCategory $parent
     * @param Collection $uCategoryAll
     */
    private function setChildrenNest(UserCategory &$parent, Collection $uCategoryAll)
    {
        //TODO isCache
        if($parent->hasChild() && !$parent->isCache('children')){
            //TODO
            $childIds = $parent->getChildIds();

            $children = [];
            foreach($uCategoryAll as $child){
                if(in_array($child->id, $childIds)){
                    $children[] = $child;
                }
            }
            foreach ($children as &$child) {
                //TODO
                $child->setParent($parent);
                //TODO Collection::wrap, get
                $child = $this->setChildrenNest(Collection::wrap($child), $uCategoryAll)->get();
            }
            //TODO setChildren
            $parent->setChildlen($children);
        }

    }

    private function getUserCategoriesWithMaster()
    {
        return $this->userCategories()
                 ->with('categoryMaster')
                 ->get();
    }
}
