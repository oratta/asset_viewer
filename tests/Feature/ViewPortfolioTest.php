<?php

namespace Tests\Feature;

use App\AssetCategoryMaster;
use App\User;
use App\UserAssetCategory;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\Compilers\Concerns\CompilesHelpers;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPortfolioTest extends TestCase
{
    use RefreshDatabase;



   /**
    * @test
    */
   public function should_DBに保存したポートフォリオ情報を指定のフォーマットで返す()
   {
       //set data
       //a user
       $this->user = factory(User::class)->states('withAsset')->create(["password" => Hash::make("secret")]);
       $this->assertDatabaseHas('user_asset_categories', ['user_id'=>$this->user->id]);
       $this->assertDatabaseHas('user_assets', ['user_id'=>$this->user->id]);
       $this->assertDatabaseHas('asset_category_masters', ['id'=>1]);

       $response = $this->actingAs($this->user)
           ->json('get', route('portfolio'));
       $response
           ->assertStatus(200);
   }
}
