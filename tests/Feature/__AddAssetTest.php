<?php

namespace Tests\Feature;

use App\User;
use App\UserAsset;
use Tests\SeedingDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class __AddAssetTest extends TestCase
{
    use RefreshDatabase;
    use SeedingDatabase;
    /**
     * @test
     */
    public function should_指定された数のuserAssetが作成される()
    {
        $this->user = factory(User::class)->states('justRegistered')->create();

        $data =[
            "count" => 10
        ];
        $response = $this->actingAs($this->user)->json('POST', route('debug.addAsset'), $data);

        $response->assertStatus(201);
        $this->assertEquals($data["count"], UserAsset::where('user_id', $this->user->id)->count());
    }
}
