<?php

namespace Tests\Feature;

use App\User;
use Tests\SeedingDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedingDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成
        $this->user = factory(User::class)->create(["password" => Hash::make("secret")]);
    }

    /**
     * @test
     */
    public function should_MFからデータをロードしたらDBにUserAssetが保存される()
    {
        $response = $this->actingAs($this->user)
            ->json('POST', route('load'));
        $response->assertStatus(200);

        $assets = UserAsset::where('user_id', $this->user->id)->all();

        $this->assertFalse($assets->count() > 0);
        $this->assertDatabaseHas('user_assets', ['id'=>1]);
    }
}
