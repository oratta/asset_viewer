<?php

namespace Tests\Feature;

use App\CategoryMaster;
use App\User;
use App\UserAsset;
use App\UserCategory;
use Tests\TestCase;
use Tests\SeedingDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditUserAssetTest extends TestCase
{
    use SeedingDatabase;
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_user_assetのリストをJsonで返却する()
    {
        $this->user = factory(User::class)->states('withAsset')->create();

        $this->assertDatabaseHas('user_assets', ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('category_masters', ['id' => 1]);

        $request = $this->actingAs($this->user)
            ->json('get', route('userAsset'));

        $category = CategoryMaster::where('section_id',1)->where('id',4)->first();
        $category->setFormattedName();
        $uAsset = UserAsset::where('user_id', $this->user->id)->first();

        $expected1 = [
            'sectionInfos' => [
                1 =>
                    [0 =>
                        [
                            'id' => $category->id,
                            'name' => $category->name
                        ]
                    ],
                ],
            ];
        $uAsset->setFormattedValue();
        $expected2 = [
            'userAssets' => [
                0 => [
                    'name' => $uAsset->name,
                    'categoryIds' => [
                        1 => $uAsset->categoryMasters->first()->id,
                    ],
                    'value' => $uAsset->value,
                ]
            ]
        ];

        $request->assertStatus(200);
        $request->assertJson($expected1);
        $request->assertJson($expected2);
    }

    /**
     * @test
     */
    public function should_user_assetにcategoryをアサインする()
    {
        $this->user = factory(User::class)->states('justRegistered')->create();
        $this->assertDatabaseHas('user_categories', ['user_id' => $this->user->id]);

        $postData = [
            '1-1' => 1,
        ];
        //存在しないデータでエラー
        $response = $this->actingAs($this->user)
            ->json('post', route('categorize.save'), $postData);
        $response->assertStatus(500);

        list($postData, $uAssets) = $this->__getPostDataUAssets();
        $response = $this->actingAs($this->user)
            ->json('post', route('categorize.save'), $postData);

        $response->assertStatus(201);
        foreach($postData as $assetId_section => $uCategoryId){
            list($assetId, $sectionNumber) = explode('-', $assetId_section);
            $this->assertDatabaseHas('user_asset_user_category', ['user_asset_id' => $assetId, 'user_category_id' => $uCategoryId]);
        }

        //current_value set to UserCategory
        $uCategory = UserCategory::where([
            ['category_master_id','=',1],
            ['user_id', '=', $this->user->id]
        ])->first();
//        $uCategory->setCurrentValue();
        $this->assertEquals('通貨', $uCategory->getName());
        $this->assertEquals($uAssets->sum('value'), $uCategory->current_value);
    }

    private function __getPostDataUAssets()
    {
        $uAssets = factory(UserAsset::class,2)->create(['user_id' => $this->user->id]);
        $sectionedUCategoryIds = $this->__getSectionedUCategoryIds();
        $postData = [];
        foreach($uAssets as $uAsset){
            $assetId = $uAsset->id;
            for($section=1;$section<=3;$section++){
                $uCategoryIds = $sectionedUCategoryIds[$section];
                $uCategoryId = $uCategoryIds[mt_rand(0, count($uCategoryIds) - 1)];
                $index = $assetId . "-" . $section;
                $postData[$index] = $uCategoryId;
            }
        }
//        $postData = [
//            '1-1' => 2, //assetId-section => categoryId
//            '1-2' => 12,
//            '1-3' => 23,
//            '2-1' => 3,
//            '2-2' => 12,
//        ];

        return [$postData, $uAssets];
    }

    private function __getSectionedUCategoryIds()
    {
        $uCategories = $this->user->userCategories()->with('categoryMaster')->get();
        $sectionedUCategoryIds = [];
        foreach ($uCategories as $uCategory) {
            if(!isset($sectionedUCategoryIds[$uCategory->categoryMaster->section_id])){
                $sectionedUCategoryIds[$uCategory->categoryMaster->section_id] = [];
            }

            if(!$uCategory->categoryMaster->isSection()){
                $sectionedUCategoryIds[$uCategory->categoryMaster->section_id][] = $uCategory->id;
            }
        }

        return $sectionedUCategoryIds;
    }

    /**
     * @test
     */
    public function should_user_assetのcategoryを更新する()
    {
        $this->user = factory(User::class)->states('justRegistered')->create();
        factory(UserAsset::class,2)->create(['user_id' => $this->user->id]);

        $postData = [
            '1-1' => 1,
            '1-2' => 12,
            '1-3' => 23,
            '2-1' => 3,
        ];
        factory(UserAsset::class,2)->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)
            ->json('post', route('categorize.save'), $postData);
        $response->assertStatus(201);


        $postData = [
            '1-1' => 1,
            '1-2' => 12,
            '1-3' => 21,
            '2-1' => 3,
        ];
        $response = $this->actingAs($this->user)
            ->json('post', route('categorize.save'), $postData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('user_asset_user_category', ['user_asset_id' => 1, 'user_category_id' => 1]);
        $this->assertDatabaseHas('user_asset_user_category', ['user_asset_id' => 1, 'user_category_id' => 12]);
        $this->assertDatabaseHas('user_asset_user_category', ['user_asset_id' => 1, 'user_category_id' => 21]);
        $this->assertDatabaseHas('user_asset_user_category', ['user_asset_id' => 2, 'user_category_id' => 3]);
    }
}
