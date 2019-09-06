<?php

namespace Tests\Feature;

use App\CategoryMaster;
use App\User;
use App\UserAsset;
use Tests\TestCase;
use Tests\SeedingDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditUserAssetTest extends TestCase
{
    use SeedingDatabase;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->user = factory(User::class)->states('withAsset')->create();
    }

    /**
     * @test
     */
    public function should_user_assetのリストをJsonで返却する()
    {

        $this->assertDatabaseHas('user_assets', ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('category_masters', ['id' => 1]);

        $request = $this->actingAs($this->user)
            ->json('get', route('userAsset'));

        $category = CategoryMaster::where('section_id',1)->first();
        $uAsset = UserAsset::where('user_id', $this->user->id)->first();

        $expected1 = [
            'sectionInfo' => [
                1 => [$category->id => $category->name],
            ],
        ];

        $expected2 = [
            'userAssetList' => [
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
}
