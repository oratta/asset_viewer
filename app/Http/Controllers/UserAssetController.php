<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use App\UserAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $sectionList = CategoryMaster::getSectionList();
        foreach ($sectionList as $sectionId => $section){
            $nameList = CategoryMaster::where('section_id', $sectionId)->pluck('name', 'id');
            $returnList[$sectionId] = $nameList;
        }
        return $returnList;
    }

    private function __getUserAssetList()
    {
        $user = Auth::user();
        $uAssetList = $user->userAssets;
        return $uAssetList;
    }
}
