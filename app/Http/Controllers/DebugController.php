<?php

namespace App\Http\Controllers;

use App\UserAsset;
use App\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addAsset(Request $request)
    {
        $count = (int)$request->input('count');
        $this->user = Auth::user();

        $uAssetList = factory(UserAsset::class,$count)->create(['user_id' => $this->user->id]);

        return response($uAssetList,201);
    }
}
