<?php

namespace App\Http\Controllers;

use App\AssetCategoryMaster;
use App\UserAsset;
use Illuminate\Http\Request;

class UserAssetController extends Controller
{
    public function index()
    {
        //get SelectInfo
        $sectionInfo = $this->__getSelectInfo();

        $userAssetList = $this->__getUserAssetList();

        return [
            'sectionInfo' => $sectionInfo,
            'userAssetList' => $userAssetList,
        ];
    }

    private function __getSelectInfo()
    {
        $returnList = [];
        $sectionList = AssetCategoryMaster::getSectionList();
        foreach ($sectionList as $sectionId => $section){
            $nameList = AssetCategoryMaster::where('section_id', $sectionId)->column('name');
            $returnList[$sectionId] = $nameList;
        }
        return $returnList;
    }

    private function __getUserAssetList()
    {
        $uAssetList = UserAsset::where('user_id', $this->user->id);
        return $uAssetList;
    }
}
