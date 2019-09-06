<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\CategoryMaster;
use Illuminate\Support\Facades\Auth;

class UserCategoryController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    public function portfolio()
    {
        $this->user = Auth::user();
        $uCategoryList = $this->user->getNestedUserCategoryList();

        return $uCategoryList->isEmpty() ? abort(204) : $uCategoryList;
    }

}
