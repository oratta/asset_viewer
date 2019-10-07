<?php

namespace App\Http\Controllers;

use App\CategoryMaster;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $category = CategoryMaster::paginate();
        return $category;
    }
}
