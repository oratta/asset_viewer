<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use App\UserAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //get SelectInfo
        $sectionInfos = $this->__getSelectInfoList();

        $userAssets = $this->__getUserAssetList();

        return [
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
        foreach ($sectionList as $sectionId => $section){
            $nameList = CategoryMaster::
                where('section_id', $sectionId)
                ->select('name', 'id', 'parent_id')
                ->with('parent')
                ->orderByRaw('id ASC')
                ->get()
                ->each(function($categoryMaster){
                    $categoryMaster->setFormattedName();
                });
            $returnList[$sectionId] = $nameList;
        }
        return $returnList;
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
