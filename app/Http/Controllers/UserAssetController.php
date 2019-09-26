<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use App\User;
use App\UserAsset;
use App\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class UserAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function($request, $next){
            $this->user = Auth::user();
            return $next($request);
        });
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
        $inputArray = $request->all();
        $saveInfo = array();
        foreach($inputArray as $key => $categoryId){
            list($assetId, $sectionId) = explode('-', $key);
            if(!isset($saveInfo[$assetId])){
                $saveInfo[$assetId] = array();
            }
            $saveInfo[$assetId][] = $categoryId;
        }

        $uAssetList = $this->user->userAssets()
                ->whereIn('id', array_keys($saveInfo))
                ->get()
                ->keyBy('id');

        if($uAssetList->count() !== count($saveInfo)){
            throw new \Exception('invalid data');
        }

        foreach($uAssetList as $uAssetId => $uAsset){
            $uAsset->userCategories()->sync($saveInfo[$uAssetId]);
        }

        $updateUCategoryIds = collect($saveInfo)->collapse()->unique()->toArray();
        $this->__saveUserCategoryCurrentValue($updateUCategoryIds);

        return response("O.K.",201);
    }

    private function __saveUserCategoryCurrentValue(array $updateUCategoryIds)
    {
        $sections = $this->__getSections($updateUCategoryIds);
        foreach($sections as $rootUCategory){
            $rootUCategory->setCurrentValue();
            $rootUCategory->save();
        }
    }

    private function __getSections(array $updateUCategoryIds)
    {
        $uCategories = UserCategory::whereIn('id', $updateUCategoryIds)
            ->with('categoryMaster')
            ->get()
            ->keyBy('id');

        $sectionIds = $uCategories->map(function($uCategory){
           return $uCategory->getSectionId();
        })->unique()->toArray();


    }

    private function __getRootNodeUCategory($updateUCategoryIds)
    {
        $candidates = UserCategory::whereIn('id', $updateUCategoryIds)->get()->keyBy('id');
        return $this->__getRootNodeCUategoryRoop($candidates);
    }

    /**
     * @param Collection $candidates key by id
     * @param array $fixList
     * @return array|mixed
     */
    private function __getRootNodeCUategoryRoop(Collection $candidates, $fixList = [])
    {
        $fixList = [];
        $candidate = $candidates->shift();
        if(!$candidate->hasParent()){
            $fixList[$candidate->id] = $candidate;
        }
        else {
            $parent = $candidate->parent;
            if(!$candidates->has($parent->id)){
                $candidates->push($parent);
            }
        }

        return $candidates->isEmpty()
            ? $fixList
            : $this->__getRootNodeCUategoryRoop($candidates, $fixList);
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
