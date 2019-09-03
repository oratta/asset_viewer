<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\AssetCategoryMaster;
use Illuminate\Support\Facades\Auth;

class UserAssetCategoryController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    public function portfolio()
    {
        $this->user = Auth::user();
        $uAssetCategoryList = $this->user->getNestedUserAssetCategoryList();

        return $uAssetCategoryList->isEmpty() ? abort(204) : $uAssetCategoryList;
    }

}
