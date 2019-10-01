<?php

namespace Tests\Unit;

use Tests\SeedingDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UserCategory;

class UserCategoryTest extends TestCase
{
    use RefreshDatabase;
    use SeedingDatabase;
    /**
     * @test
     */
    public function testIsCache_forRelation()
    {
        $uCategory = factory(UserCategory::class)->create();
        $this->assertFalse($uCategory->isCached('categoryMaster'));
        $a = $uCategory->categoryMaster;
        $this->assertTrue($uCategory->isCached('categoryMaster'));
    }

    /**
     * @test
     */
    public function testIsCache_forMutate()
    {

    }

    public function testIsCache_forNormal()
    {
        
    }
}
