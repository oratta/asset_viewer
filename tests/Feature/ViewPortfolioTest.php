<?php

namespace Tests\Feature;

use App\CategoryMaster;
use App\User;
use App\UserCategory;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\Compilers\Concerns\CompilesHelpers;
use Tests\SeedingDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPortfolioTest extends TestCase
{
    use RefreshDatabase;
    use SeedingDatabase;


   /**
    * @test
    */
   public function should_DBに保存したポートフォリオ情報を指定のフォーマットで返す()
   {
       //set data
       //a user
       $this->user = factory(User::class)->states('withAsset')->create(["password" => Hash::make("secret")]);
       $this->assertDatabaseHas('user_categories', ['user_id'=>$this->user->id]);
       $this->assertDatabaseHas('user_assets', ['user_id'=>$this->user->id]);
       $this->assertDatabaseHas('category_masters', ['id'=>1]);

       $response = $this->actingAs($this->user)
           ->json('get', route('portfolio'));
       $response->assertStatus(200);
       $this->__checkJson($response);
   }

    /**
     * @test
     */
   public function should_user_categoriesにデータが無かったらエラーを返す()
   {
       $this->user = factory(User::class)->create();
       $this->assertDatabaseMissing('user_categories', ['user_id' => $this->user->id]);
       $response = $this->actingAs($this->user)
            ->json('get', route('portfolio'));
       $response->assertStatus(204);
   }

   private function __checkJson($response)
   {
       $masters = CategoryMaster::whereIn('id', [1, 2, 3])->get();
       $jsonExpected =
           [
               ['name'=>$masters[0]->name],
               ['name'=>$masters[1]->name],
               ['name'=>$masters[2]->name]
           ];
       $response
           ->assertStatus(200)
           ->assertJson($jsonExpected);
   }
}
