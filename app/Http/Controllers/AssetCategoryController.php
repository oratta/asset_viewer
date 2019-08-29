<?php

namespace App\Http\Controllers;

use App\AssetCategoryMaster;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * カテゴリー一覧
     */
    public function index()
    {
        $assetCategory = AssetCategoryMaster::with(['owner', 'likes'])
            ->paginate();

        return $assetCategory;
    }
}
