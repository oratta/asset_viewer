<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use App\UserAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Collection;

class UserAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //get SelectInfo
        list($sectionInfos, $sections) = $this->__getSelectInfoList();

        $userAssets = $this->__getUserAssetList();

        return [
            'sections' => $sections,
            'sectionInfos' => $sectionInfos,
            'userAssets' => $userAssets,
        ];
    }

    public function categorize(Request $request)
    {
        //{assetId -> [section_id => 1,,,],,}
        $assignList = $request->input('assetList');
        $uAssetList = $this->user->userAssets()->with('userCategories')->key('id');
        foreach($uAssetList as $uAssetId => $uAsset){
            $uAsset->userCategories()->sync($assignList[$uAssetId]);
            $uAsset->userCategories->each(function($uCategory){
                $uCategory->setCurrentValue();
                $uCategory->save();
            });
        }

        return response("O.K.",201);
    }

    private function __getSelectInfoList()
    {
        $returnList = [];
        $sectionList = CategoryMaster::getSectionList();
        $categoryAll = CategoryMaster::all();
        $categoryAll = $this->__getCategoriesWithParents($categoryAll);
        foreach ($sectionList as $sectionId => $section){
            $nameList = $categoryAll->where('section_id', $sectionId)
                ->each(function($categoryMaster){
                    $categoryMaster->setFormattedName();
                })->filter(function($categoryMaster){
                    return !$categoryMaster->has_child;
                });
            $returnList[$sectionId] = $nameList->values();
        }
        return [$returnList, $sectionList];
    }

    private function __getCategoriesWithParents(\Illuminate\Support\Collection &$categoryMasters)
    {
        $keyByIdList = $categoryMasters->keyBy('id');
        foreach($keyByIdList as $id => &$category){
            if($category->hasParent()){
                $category->parent = $keyByIdList->get($category->parent_id);
            }
        }
        return $keyByIdList;
    }

    private function __getUserAssetList()
    {
        $user = Auth::user();
        $uAssetList = $user->userAssets()->with(['userCategories', 'userCategories.categoryMaster'])->get()
                        ->each(function($userAsset){
                            $userAsset->setFormattedValue();
                        });
        return $uAssetList;
    }
}
