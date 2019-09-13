<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Exception;

class UserAsset extends Model
{
    /** JSONに含める属性の指定 */
    protected $visible = [
        'categoryIds',
        'name',
        'account',
        'value',
    ];

    protected $appends = [
        'categoryIds',
        'categoryMasters',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userCategories()
    {
        return $this->belongsToMany('App\UserCategory');
    }

    public function getCategoryMastersAttribute()
    {
        return $this->userCategories->map(function ($uCategory) {
            return $uCategory->categoryMaster;
        });
    }

    public function getCategoryIdsAttribute()
    {
        $returnList = [];
        foreach ($this->categoryMasters as $category){
            if(isset($returnList[$category->section_id])){
                throw new Exception('invalid data');
            }
            $returnList[$category->section_id] = $category->id;
        }

        return $returnList;
    }

    public function save(array $options = [])
    {
        if (!$this->validateSave()){
            throw new \Exception("user asset can't be categorize categories have any children");
        }

        parent::save($options);
    }

    /**
     * @return bool
     */
    private function validateSave()
    {
        foreach ($this->userCategories as $uCategory){
            if($uCategory->hasChild()){
                return false;
            }
        }
        return true;
    }

    protected function getFormattedValue()
    {
        return number_format($this->value);
    }

    public function setFormattedValue()
    {
        $this->value = $this->getFormattedValue();
    }
}
