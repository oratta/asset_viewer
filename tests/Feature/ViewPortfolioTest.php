<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
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
       $this->user = factory(User::class)->create(["password" => Hash::make("secret")]);


       //1 section 1 categories


       //multi section multi categories

   }
}
