<?php

namespace App\Http\Controllers;

use App\UserAsset;
use App\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function addAsset(Request $request)
    {
        $count = $request->input('count');
        $this->user = Auth::user();

        $uCategoryList = $this->user->userCategories;
        $created = [];
        foreach($uCategoryList as $uCategory){
            if(!$uCategory->hasChild()){
                $uAssetList = factory(UserAsset::class,$count)->make(['user_id' => $this->user->id]);
                $uCategory->userAssets()->saveMany($uAssetList);
                $created[$uCategory->id] = $uAssetList;
            }
        }
        foreach($uCategoryList as $uCategory){
            if($uCategory->hasChild()){
                $uCategory->setCurrentValue();
                $uCategory->save();
            }
        }

        return response($created,201);
    }
}
