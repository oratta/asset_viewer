<?php

namespace Tests\Unit;

use Tests\SeedingDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UserCategory;

class UserAssetTest extends TestCase
{
    use RefreshDatabase;
    use SeedingDatabase;
    /**
     * @test
     */
    public function testCategoriesAuto()
    {
        $userAsset = factory(UserAsset::class)->create();
        $userAsset->categoriesAuto();
    }
}
